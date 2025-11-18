<?php
// api/auto_check.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Check global auto_mode
$auto_mode = 0;
$res = $conn->query("SELECT setting_value FROM system_settings WHERE setting_key = 'auto_mode' LIMIT 1");
if ($res && $row = $res->fetch_assoc()) $auto_mode = intval($row['setting_value']);
if ($auto_mode !== 1) {
    echo json_encode(['success'=>true, 'auto_mode'=>0, 'msg'=>'auto mode disabled']);
    exit;
}

// Helper: get last value from sensor table
function get_last($conn, $table, $col) {
    $q = $conn->query("SELECT $col FROM $table ORDER BY id DESC LIMIT 1");
    if ($q && $r = $q->fetch_assoc()) return floatval($r[$col]);
    return null;
}

// load thresholds
$th = [];
$res = $conn->query("SELECT sensor_type, min_value, max_value, actuator_code FROM sensor_thresholds");
while ($row = $res->fetch_assoc()) {
    $th[$row['sensor_type']] = $row;
}

// read latest sensors
$latest = [
    'temperature' => get_last($conn, 'sensor_suhu_kelembapan', 'suhu'),
    'humidity'    => get_last($conn, 'sensor_suhu_kelembapan', 'kelembapan'),
    'light'       => get_last($conn, 'sensor_ldr', 'nilai_ldr'),
    'soil'        => get_last($conn, 'sensor_soil', 'soil')
];

$actions = [];
foreach ($th as $stype => $cfg) {
    $act_code = $cfg['actuator_code'];
    if (!$act_code) continue; // no actuator assigned

    $sensor_val = isset($latest[$stype]) ? $latest[$stype] : null;
    if ($sensor_val === null) continue; // no sensor value

    $should_on = null;
    if ($cfg['min_value'] !== null && $sensor_val < $cfg['min_value']) {
        // below min: maybe need to turn ON (depends on sensor logic)
        // For pump: low soil (%) -> turn ON (to water). For temperature: below min -> maybe ON heater (not present)
        // We'll assume higher actuator when value < min => turn ON (this logic can be adapted)
        $should_on = 1;
    }
    if ($cfg['max_value'] !== null && $sensor_val > $cfg['max_value']) {
        // above max: turn ON for fan/light? This decision is domain-specific.
        // Common for temperature: if temp > max -> turn ON fan (cooling)
        $should_on = 1;
    }
    // For typical use: implement explicit mapping per sensor:
    // temperature -> fan if > max
    // light -> light if < min (turn on)
    // soil -> pump if < min (turn on)
    // We'll implement explicit mapping:
    $should_on = null;
    if ($stype === 'temperature') {
        if ($cfg['max_value'] !== null && $sensor_val > $cfg['max_value']) $should_on = 1; // turn fan ON when too hot
        else $should_on = 0;
    } elseif ($stype === 'humidity') {
        // skip automatic action if no actuator assigned
        $should_on = 0;
    } elseif ($stype === 'light') {
        if ($cfg['min_value'] !== null && $sensor_val < $cfg['min_value']) $should_on = 1; // turn light ON when too dark
        else $should_on = 0;
    } elseif ($stype === 'soil') {
        if ($cfg['min_value'] !== null && $sensor_val < $cfg['min_value']) $should_on = 1; // turn pump ON when soil too dry
        else $should_on = 0;
    }

    // Check manual_override for actuator
    $code = $conn->real_escape_string($act_code);
    $q = $conn->query("SELECT status, manual_override FROM actuator WHERE code = '$code' LIMIT 1");
    if (!$q) continue;
    $row = $q->fetch_assoc();
    if ($row && intval($row['manual_override']) === 1) {
        // skip automatic change
        continue;
    }

    // Update actuator if different
    if ($row && intval($row['status']) !== intval($should_on)) {
        $stmt = $conn->prepare("UPDATE actuator SET status = ?, last_auto_trigger = NOW(), updated_at = NOW() WHERE code = ?");
        if ($stmt) {
            $stmt->bind_param("is", $should_on, $code);
            $stmt->execute();
            $stmt->close();

            $detail = sprintf("Auto-mode: set %s => %s based on %s (val=%.2f)", $code, $should_on ? 'ON' : 'OFF', $stype, $sensor_val);
            $stmt2 = $conn->prepare("INSERT INTO log_aktivitas (detail) VALUES (?)");
            if ($stmt2) { $stmt2->bind_param("s", $detail); $stmt2->execute(); $stmt2->close(); }

            $actions[] = ['actuator' => $code, 'status' => intval($should_on), 'sensor'=>$stype, 'value'=>floatval($sensor_val)];
        }
    }
}

echo json_encode(['success'=>true, 'actions'=>$actions, 'latest'=>$latest]);
?>
