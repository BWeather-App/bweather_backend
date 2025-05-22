<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../config/config.php';
require_once '../routes/api.php';

// Folder yang berfungsi sebagai root directory untuk akses publik.
// Di sinilah biasanya file index.php berada.
// File ini adalah entry point aplikasi.