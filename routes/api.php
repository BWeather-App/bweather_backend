<?php
require_once '../controllers/WeatherController.php';
require_once '../controllers/SearchController.php';

$searchController = new SearchController();
$weatherController = new WeatherController();

$endpoint = $_GET['endpoint'] ?? null;
$latitude = $_GET['latitude'] ?? null;
$longitude = $_GET['longitude'] ?? null;

if ($endpoint === 'weather' && $latitude && $longitude) {
    $weatherController->getWeatherByGPS($latitude, $longitude);
} elseif ($endpoint === 'search' && isset($_GET['query'])) {
    $searchController->searchByCityName($_GET['query']);
} else {
    header("HTTP/1.0 400 Bad Request");
    echo json_encode(['error' => 'Parameter kurang lengkap atau endpoint tidak ditemukan']);
}
