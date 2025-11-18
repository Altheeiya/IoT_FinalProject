<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    if (!isset($_POST['mode'])) {
        throw new Exception('Mode not provided');
    }

    $mode = $_POST['mode']; // 'manual' or 'auto'

    if (!in_array($mode, ['manual', 'auto'])) {
        throw new Exception('Invalid mode');
    }

    // Update system settings
    $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'auto_mode'");
    $mode_value = $mode === 'auto' ? '1' : '0';
    $stmt->bind_param("s", $mode_value);
    $stmt->execute();

    // Log activity
    $detail = "System mode changed to: " . strtoupper($mode);
    $conn->query("INSERT INTO log_aktivitas (detail) VALUES ('$detail')");

    echo json_encode([
        'success' => true,
        'mode' => $mode,
        'msg' => 'Mode updated successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}
