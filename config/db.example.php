<?php
// Database Configuration
// COPY THIS FILE and rename to db.php
// Then fill with your database credentials

$host = 'localhost';
$user = 'root';
$pass = '';  // Your MySQL password
$db   = 'greenhouse_db';  // Your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
