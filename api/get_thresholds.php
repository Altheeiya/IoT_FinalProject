<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $thresholds = [];

    $res = $conn->query("SELECT sensor_type, min_value, max_value FROM sensor_thresholds");

    while ($row = $res->fetch_assoc()) {
        $thresholds[$row['sensor_type']] = [
            'min_value' => $row['min_value'],
            'max_value' => $row['max_value']
        ];
    }

    echo json_encode([
        'success' => true,
        'thresholds' => $thresholds
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}
