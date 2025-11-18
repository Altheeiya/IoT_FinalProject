<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    // Truncate log table
    $conn->query("TRUNCATE TABLE log_aktivitas");

    // Add a new log entry
    $conn->query("INSERT INTO log_aktivitas (detail) VALUES ('Log history cleared')");

    echo json_encode([
        'success' => true,
        'msg' => 'Logs cleared successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}
