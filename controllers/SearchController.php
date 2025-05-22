<?php
require_once '../config/config.php';
require_once 'WeatherController.php';

class SearchController {
    public function searchByCityName($query) {
        $location = urlencode($query);
        $url = TOMORROW_BASE_URL . "/realtime?location={$location}&apikey=" . TOMORROW_API_KEY;

        $response = $this->fetchData($url);
        $data = json_decode($response, true);

        if (!isset($data['location']['lat']) || !isset($data['location']['lon'])) {
            header("HTTP/1.0 404 Not Found");
            echo json_encode(['error' => 'Kota tidak ditemukan']);
            return;
        }

        $latitude = $data['location']['lat'];
        $longitude = $data['location']['lon'];
        $name = $data['location']['name'];

        // Ambil data cuaca
        $weatherController = new WeatherController();
        $weatherData = $weatherController->getWeatherByGPS($latitude, $longitude, true); // mode silent / return data saja

        // Gabungkan dengan info lokasi
        $result = [
            'location' => [
                'lat' => $latitude,
                'lon' => $longitude,
                'name' => $name
            ],
            'weather' => $weatherData
        ];

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    private function fetchData($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
