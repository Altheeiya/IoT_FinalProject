<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    if (!isset($_POST['device_code'])) {
        throw new Exception('Device code not provided');
    }

    $device_code = $_POST['device_code'];
    $ip_address = isset($_POST['ip']) ? $_POST['ip'] : null;

    // Update or insert device status
    $stmt = $conn->prepare("INSERT INTO device_status (device_code, last_seen, is_online, ip_address) 
                           VALUES (?, NOW(), 1, ?) 
                           ON DUPLICATE KEY UPDATE last_seen = NOW(), is_online = 1, ip_address = ?");
    $stmt->bind_param("sss", $device_code, $ip_address, $ip_address);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'device' => $device_code,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}
