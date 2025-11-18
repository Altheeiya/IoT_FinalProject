<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $device = isset($_GET['device']) ? $_GET['device'] : null;

    if (!$device) {
        throw new Exception('Device parameter required');
    }

    $response = [];

    // Get actuator statuses based on device
    if ($device === 'ESP1') {
        // ESP1 controls fan and light
        $res = $conn->query("SELECT code, status FROM actuator WHERE code IN ('fan', 'light')");
        while ($row = $res->fetch_assoc()) {
            $response[$row['code']] = intval($row['status']);
        }
    } else if ($device === 'ESP2') {
        // ESP2 controls pump
        $res = $conn->query("SELECT code, status FROM actuator WHERE code = 'pump'");
        while ($row = $res->fetch_assoc()) {
            $response[$row['code']] = intval($row['status']);
        }
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
