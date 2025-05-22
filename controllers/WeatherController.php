<?php
require_once '../config/config.php';

class WeatherController {
   public function getWeatherByGPS($latitude, $longitude, $returnData = false) {
    $lokasi = $latitude . "," . $longitude;

    $perJam = $this->getHourlyForecast($lokasi);
    $astronomi = $this->getDailyAstronomy($lokasi);
    $kemarin = $this->getYesterdayWeather($lokasi);
    $realtime = $this->getRealtimeWeather($lokasi);

    $hariIni = date('Y-m-d');
    $besok = date('Y-m-d', strtotime('+1 day'));
    $lusa = date('Y-m-d', strtotime('+2 day'));
    $hariKe3 = date('Y-m-d', strtotime('+3 day'));

    $dataCuaca = [
        'cuaca_saat_ini' => $realtime,
        'kemarin' => $this->mapHourlyData($kemarin),
        'hari_ini' => $this->gabungCuacaAstronomi($perJam[$hariIni] ?? [], $astronomi[$hariIni] ?? []),
        'besok' => $this->gabungCuacaAstronomi($perJam[$besok] ?? [], $astronomi[$besok] ?? []),
        'lusa' => $this->gabungCuacaAstronomi($perJam[$lusa] ?? [], $astronomi[$lusa] ?? []),
        'hari_ke_3' => $this->gabungCuacaAstronomi($perJam[$hariKe3] ?? [], $astronomi[$hariKe3] ?? []),
    ];

    if ($returnData) {
        return $dataCuaca; // ✅ hanya return tanpa echo/header
    }

    header('Content-Type: application/json');
    echo json_encode($dataCuaca);
}

    private function getRealtimeWeather($lokasi) {
        $url = TOMORROW_BASE_URL . "/realtime?location=" . urlencode($lokasi) . "&apikey=" . TOMORROW_API_KEY;
        $response = $this->fetchData($url);
        $data = json_decode($response, true);

        if (!isset($data['data']['values'])) return ['kesalahan' => 'Data realtime tidak ditemukan'];

        $v = $data['data']['values'];
        $utc = $data['data']['time'];
        $wib = $this->convertToWIB($utc);

        return [
            'waktu' => $wib,
            'suhu' => $v['temperature'] ?? null,
            'kelembapan' => $v['humidity'] ?? null,
            'kecepatan_angin' => $v['windSpeed'] ?? null,
            'arah_angin' => $v['windDirection'] ?? null,
            'tekanan_udara' => $v['pressureSurfaceLevel'] ?? null,
            'indeks_uv' => $v['uvIndex'] ?? null,
            'terasa_seperti' => $v['temperatureApparent'] ?? null,
            'peluang_hujan' => $v['precipitationProbability'] ?? null
        ];
    }

    private function getYesterdayWeather($lokasi) {
        $url = TOMORROW_BASE_URL . '/history/recent?location=' . urlencode($lokasi) . '&apikey=' . TOMORROW_API_KEY;
        $response = $this->fetchData($url);
        $data = json_decode($response, true);
        if (!isset($data['timelines']['hourly'])) return [];

        $tanggal = date('Y-m-d', strtotime('-1 day'));
        $kelompok = $this->groupByDate($data['timelines']['hourly']);
        return $kelompok[$tanggal] ?? [];
    }

    private function getHourlyForecast($lokasi) {
        $url = TOMORROW_BASE_URL . '/forecast?location=' . urlencode($lokasi) . '&apikey=' . TOMORROW_API_KEY;
        $response = $this->fetchData($url);
        $data = json_decode($response, true);
        if (!isset($data['timelines']['hourly'])) return [];

        return $this->groupByDate($data['timelines']['hourly']);
    }

    private function getDailyAstronomy($lokasi) {
        $url = TOMORROW_BASE_URL . '/forecast?location=' . urlencode($lokasi) . '&apikey=' . TOMORROW_API_KEY;
        $response = $this->fetchData($url);
        $data = json_decode($response, true);

        $hasil = [];
        if (isset($data['timelines']['daily'])) {
            foreach ($data['timelines']['daily'] as $item) {
                $tanggal = substr($item['time'], 0, 10);
                $v = $item['values'];
                $hasil[$tanggal] = [
                    'matahari_terbit' => $this->convertToWIB($v['sunriseTime'] ?? null),
                    'matahari_terbenam' => $this->convertToWIB($v['sunsetTime'] ?? null),
                    'bulan_terbit' => $this->convertToWIB($v['moonriseTime'] ?? null),
                    'bulan_terbenam' => $this->convertToWIB($v['moonsetTime'] ?? null)
                ];
            }
        }

        return $hasil;
    }

    private function gabungCuacaAstronomi($cuacaPerJam, $astronomi) {
        $hasil = [];
        foreach ($cuacaPerJam as $item) {
            $waktuWIB = $this->convertToWIB($item['time']);
            $v = $item['values'];

            $hasil[] = [
                'waktu' => $waktuWIB,
                'suhu' => $v['temperature'] ?? null,
                'kelembapan' => $v['humidity'] ?? null,
                'kecepatan_angin' => $v['windSpeed'] ?? null,
                'arah_angin' => $v['windDirection'] ?? null,
                'tekanan_udara' => $v['pressureSurfaceLevel'] ?? null,
                'indeks_uv' => $v['uvIndex'] ?? null,
                'terasa_seperti' => $v['temperatureApparent'] ?? null,
                'peluang_hujan' => $v['precipitationProbability'] ?? null,
                'matahari_terbit' => $astronomi['matahari_terbit'] ?? null,
                'matahari_terbenam' => $astronomi['matahari_terbenam'] ?? null,
                'bulan_terbit' => $astronomi['bulan_terbit'] ?? null,
                'bulan_terbenam' => $astronomi['bulan_terbenam'] ?? null
            ];
        }
        return $hasil;
    }

    private function mapHourlyData($data) {
        $hasil = [];
        foreach ($data as $item) {
            $waktuWIB = $this->convertToWIB($item['time']);
            $v = $item['values'];

            $hasil[] = [
                'waktu' => $waktuWIB,
                'suhu' => $v['temperature'] ?? null,
                'kelembapan' => $v['humidity'] ?? null,
                'kecepatan_angin' => $v['windSpeed'] ?? null,
                'arah_angin' => $v['windDirection'] ?? null,
                'tekanan_udara' => $v['pressureSurfaceLevel'] ?? null,
                'indeks_uv' => $v['uvIndex'] ?? null,
                'terasa_seperti' => $v['temperatureApparent'] ?? null,
                'peluang_hujan' => $v['precipitationProbability'] ?? null
            ];
        }
        return $hasil;
    }

    private function convertToWIB($utc) {
        if (!$utc) return null;
        $dt = new DateTime($utc);
        $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
        return $dt->format('Y-m-d H:i:s');
    }

    private function groupByDate($data) {
        $kelompok = [];
        foreach ($data as $item) {
            if (!isset($item['time'])) continue;
            $tanggal = substr($item['time'], 0, 10);
            $kelompok[$tanggal][] = $item;
        }
        return $kelompok;
    }

    private function fetchData($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respon = curl_exec($ch);
        curl_close($ch);
        return $respon;
    }
}
