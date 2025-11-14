<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Ambil data dari POST
$suhu = isset($_POST['suhu']) ? floatval($_POST['suhu']) : null;
$kelembapan = isset($_POST['kelembapan']) ? floatval($_POST['kelembapan']) : null;
$ldr = isset($_POST['ldr']) ? intval($_POST['ldr']) : null;

// Debug: simpan POST untuk cek
file_put_contents('debug.txt', date('Y-m-d H:i:s') . " - " . print_r($_POST, true) . "\n", FILE_APPEND);

if ($suhu === null || $kelembapan === null || $ldr === null) {
    echo json_encode(['success'=>false, 'msg'=>'parameter tidak lengkap']);
    exit;
}

// Simpan suhu & kelembapan
$stmt1 = $conn->prepare("INSERT INTO sensor_suhu_kelembapan (suhu, kelembapan) VALUES (?, ?)");
$stmt1->bind_param("dd", $suhu, $kelembapan);
$ok1 = $stmt1->execute();
$stmt1->close();

// Simpan LDR
$stmt2 = $conn->prepare("INSERT INTO sensor_ldr (nilai_ldr) VALUES (?)");
$stmt2->bind_param("i", $ldr);
$ok2 = $stmt2->execute();
$stmt2->close();

// Balikan respon
if ($ok1 && $ok2) echo json_encode(['success'=>true]);
else echo json_encode(['success'=>false, 'msg'=>$conn->error]);
?>
