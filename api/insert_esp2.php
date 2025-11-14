<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$soil = isset($_REQUEST['soil']) ? intval($_REQUEST['soil']) : null;

if ($soil === null) {
    echo json_encode(['success'=>false,'msg'=>'parameter soil diperlukan']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO sensor_soil (soil) VALUES (?)");
$stmt->bind_param("i", $soil);
$ok = $stmt->execute();
$stmt->close();

if ($ok) echo json_encode(['success'=>true]);
else echo json_encode(['success'=>false,'msg'=>$conn->error]);
?>
