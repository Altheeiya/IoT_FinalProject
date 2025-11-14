<?php
// api/update_actuator.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$input = $_POST; // form-encoded
$code = isset($input['code']) ? $conn->real_escape_string($input['code']) : null;
$status = isset($input['status']) ? intval($input['status']) : null;

if (!$code || ($status !== 0 && $status !== 1 && $status !== '0' && $status !== '1')) {
    echo json_encode(['success'=>false,'msg'=>'parameter tidak valid']);
    exit;
}

// update actuator
$stmt = $conn->prepare("UPDATE actuator SET status = ?, updated_at = NOW() WHERE code = ?");
$stmt->bind_param("is", $status, $code);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    // log
    $detail = sprintf("Aktuator %s diubah menjadi %s", $code, $status ? 'ON' : 'OFF');
    $stmt2 = $conn->prepare("INSERT INTO log_aktivitas (detail) VALUES (?)");
    $stmt2->bind_param("s", $detail);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'msg'=>$conn->error]);
}
?>
