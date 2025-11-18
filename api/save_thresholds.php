<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['thresholds'])) {
        throw new Exception('Thresholds data not provided');
    }

    $thresholds = $data['thresholds'];

    // Update each threshold
    foreach ($thresholds as $sensor => $values) {
        $min = isset($values['min']) && $values['min'] !== '' ? floatval($values['min']) : null;
        $max = isset($values['max']) && $values['max'] !== '' ? floatval($values['max']) : null;

        $stmt = $conn->prepare("UPDATE sensor_thresholds SET min_value = ?, max_value = ? WHERE sensor_type = ?");
        $stmt->bind_param("dds", $min, $max, $sensor);
        $stmt->execute();
    }

    // Log activity
    $conn->query("INSERT INTO log_aktivitas (detail) VALUES ('Threshold settings updated')");

    echo json_encode([
        'success' => true,
        'msg' => 'Thresholds saved successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}
