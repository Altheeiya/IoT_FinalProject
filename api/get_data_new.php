<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Get date filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Build WHERE clause for date filtering
$date_where = "";
if ($start_date && $end_date) {
    $date_where = " WHERE waktu BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
}

// --- Data sensor terbaru ---
$latest = [];

// Suhu & kelembapan
$query = "SELECT suhu, kelembapan, waktu FROM sensor_suhu_kelembapan ORDER BY id DESC LIMIT 1";
$res = $conn->query($query);
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

// --- History chart (with date filtering) ---
$history = ['temp' => [], 'humidity' => [], 'soil' => [], 'light' => []];

// Suhu
$query = "SELECT suhu AS value, waktu AS ts FROM sensor_suhu_kelembapan" . $date_where . " ORDER BY id DESC LIMIT 50";
$res = $conn->query($query);
while ($row = $res->fetch_assoc()) $history['temp'][] = ['ts' => $row['ts'], 'value' => floatval($row['value'])];

// Kelembapan udara
$query = "SELECT kelembapan AS value, waktu AS ts FROM sensor_suhu_kelembapan" . $date_where . " ORDER BY id DESC LIMIT 50";
$res = $conn->query($query);
while ($row = $res->fetch_assoc()) $history['humidity'][] = ['ts' => $row['ts'], 'value' => floatval($row['value'])];

// Soil
$query = "SELECT soil AS value, waktu AS ts FROM sensor_soil" . $date_where . " ORDER BY id DESC LIMIT 50";
$res = $conn->query($query);
while ($row = $res->fetch_assoc()) $history['soil'][] = ['ts' => $row['ts'], 'value' => floatval($row['value'])];

// Light
$query = "SELECT nilai_ldr AS value, waktu AS ts FROM sensor_ldr" . $date_where . " ORDER BY id DESC LIMIT 50";
$res = $conn->query($query);
while ($row = $res->fetch_assoc()) $history['light'][] = ['ts' => $row['ts'], 'value' => intval($row['value'])];

// Reverse arrays to show oldest first
$history['temp'] = array_reverse($history['temp']);
$history['humidity'] = array_reverse($history['humidity']);
$history['soil'] = array_reverse($history['soil']);
$history['light'] = array_reverse($history['light']);

// --- Actuators ---
$actuators = [];
$res = $conn->query("SELECT code, status FROM actuator");
while ($row = $res->fetch_assoc()) {
    $actuators[$row['code']] = ['status' => intval($row['status'])];
}

// --- Logs ---
$logs = [];
$res = $conn->query("SELECT detail, created_at FROM log_aktivitas ORDER BY id DESC LIMIT 15");
while ($row = $res->fetch_assoc()) {
    $logs[] = ['detail' => $row['detail'], 'created_at' => $row['created_at']];
}

// --- Device Status ---
$devices = [];
$res = $conn->query("SELECT device_code, is_online, last_seen FROM device_status");
while ($row = $res->fetch_assoc()) {
    $devices[$row['device_code']] = [
        'is_online' => intval($row['is_online']),
        'last_seen' => $row['last_seen']
    ];
}

// Check if devices are still online (consider offline if last seen > 30 seconds ago)
foreach ($devices as $code => $device) {
    $last_seen_time = strtotime($device['last_seen']);
    $current_time = time();
    if (($current_time - $last_seen_time) > 30) {
        $devices[$code]['is_online'] = 0;
        // Update database
        $conn->query("UPDATE device_status SET is_online = 0 WHERE device_code = '$code'");
    }
}

// --- Output JSON ---
echo json_encode([
    'success' => true,
    'latest' => $latest,
    'history' => $history,
    'actuators' => $actuators,
    'logs' => $logs,
    'devices' => $devices
]);
