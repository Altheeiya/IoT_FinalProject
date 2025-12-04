<?php
// config/db.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';        // isi jika punya password
$DB_NAME = 'greenhouse_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'DB connection failed: ' . $conn->connect_error]));
}
$conn->set_charset("utf8mb4");
