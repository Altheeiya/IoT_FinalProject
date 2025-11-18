<?php
// api/update_actuator.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$input = $_POST;
$code = isset($input['code']) ? $conn->real_escape_string($input['code']) : null;
$status = isset($input['status']) ? intval($input['status']) : null;

if (!$code || ($status !== 0 && $status !== 1)) {
    echo json_encode(['success'=>false,'msg'=>'parameter tidak valid']);
    exit;
}

// Prepare update: set manual_override=1 so auto won't immediately overwrite
$stmt = $conn->prepare("UPDATE actuator SET status = ?, manual_override = 1, updated_at = NOW() WHERE code = ?");
if (!$stmt) {
    echo json_encode(['success'=>false,'msg'=>'prepare failed','error'=>$conn->error]);
    exit;
}
$stmt->bind_param("is", $status, $code);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    // log activity
    $detail = sprintf("Aktuator %s diubah menjadi %s (manual)", $code, $status ? 'ON' : 'OFF');
    $stmt2 = $conn->prepare("INSERT INTO log_aktivitas (detail) VALUES (?)");
    if ($stmt2) {
        $stmt2->bind_param("s", $detail);
        $stmt2->execute();
        $stmt2->close();
    }

    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'msg'=>$conn->error]);
}
?>
