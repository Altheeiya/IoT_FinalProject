<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// --- Data sensor terbaru ---
$latest = [];

// Suhu & kelembapan
$res = $conn->query("SELECT suhu, kelembapan, waktu FROM sensor_suhu_kelembapan ORDER BY id DESC LIMIT 1");
$row = $res->fetch_assoc();
$latest['temperature'] = $row ? floatval($row['suhu']) : null;
$latest['humidity'] = $row ? floatval($row['kelembapan']) : null;
$latest['ts'] = $row ? $row['waktu'] : null;

// LDR
$res = $conn->query("SELECT nilai_ldr, waktu FROM sensor_ldr ORDER BY id DESC LIMIT 1");
$row = $res->fetch_assoc();
$latest['light'] = $row ? intval($row['nilai_ldr']) : null;

// Soil
$res = $conn->query("SELECT soil, waktu FROM sensor_soil ORDER BY id DESC LIMIT 1");
$row = $res->fetch_assoc();
$latest['soil'] = $row ? floatval($row['soil']) : null;

// --- History chart (ambil 24 titik terakhir per sensor) ---
$history = ['temp'=>[], 'soil'=>[], 'light'=>[]];

// Suhu
$res = $conn->query("SELECT suhu AS value, waktu AS ts FROM sensor_suhu_kelembapan ORDER BY id DESC LIMIT 24");
while($row = $res->fetch_assoc()) $history['temp'][] = ['ts'=>$row['ts'], 'value'=>floatval($row['value'])];

// Soil
$res = $conn->query("SELECT soil AS value, waktu AS ts FROM sensor_soil ORDER BY id DESC LIMIT 24");
while($row = $res->fetch_assoc()) $history['soil'][] = ['ts'=>$row['ts'], 'value'=>floatval($row['value'])];

// Light
$res = $conn->query("SELECT nilai_ldr AS value, waktu AS ts FROM sensor_ldr ORDER BY id DESC LIMIT 24");
while($row = $res->fetch_assoc()) $history['light'][] = ['ts'=>$row['ts'], 'value'=>intval($row['value'])];

// --- Actuators (ambil semua actuator dari tabel) ---
$actuators = [];
$res = $conn->query("SELECT code, status FROM actuator");
while($row = $res->fetch_assoc()) $actuators[$row['code']] = ['status'=>intval($row['status'])];

// --- Logs (ambil 10 terakhir) ---
$logs = [];
$res = $conn->query("SELECT detail, created_at FROM log_aktivitas ORDER BY id DESC LIMIT 10");
while($row = $res->fetch_assoc()) $logs[] = ['detail'=>$row['detail'], 'created_at'=>$row['created_at']];

// --- Output JSON ---
echo json_encode([
    'success' => true,
    'latest' => $latest,
    'history' => $history,
    'actuators' => $actuators,
    'logs' => $logs
]);
?>
