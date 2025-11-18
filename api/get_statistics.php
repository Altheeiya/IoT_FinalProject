<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $period = isset($_GET['period']) ? $_GET['period'] : 'today';

    $date_condition = '';

    switch ($period) {
        case 'today':
            $date_condition = "DATE(waktu) = CURDATE()";
            break;
        case 'week':
            $date_condition = "waktu >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $date_condition = "waktu >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        default:
            $date_condition = "DATE(waktu) = CURDATE()";
    }

    $stats = [];

    // Temperature statistics
    $query = "SELECT MIN(suhu) as min, MAX(suhu) as max, AVG(suhu) as avg 
              FROM sensor_suhu_kelembapan 
              WHERE $date_condition";
    $res = $conn->query($query);
    $row = $res->fetch_assoc();
    $stats['temperature'] = [
        'min' => $row['min'],
        'max' => $row['max'],
        'avg' => $row['avg']
    ];

    // Humidity statistics
    $query = "SELECT MIN(kelembapan) as min, MAX(kelembapan) as max, AVG(kelembapan) as avg 
              FROM sensor_suhu_kelembapan 
              WHERE $date_condition";
    $res = $conn->query($query);
    $row = $res->fetch_assoc();
    $stats['humidity'] = [
        'min' => $row['min'],
        'max' => $row['max'],
        'avg' => $row['avg']
    ];

    // Light statistics
    $query = "SELECT MIN(nilai_ldr) as min, MAX(nilai_ldr) as max, AVG(nilai_ldr) as avg 
              FROM sensor_ldr 
              WHERE $date_condition";
    $res = $conn->query($query);
    $row = $res->fetch_assoc();
    $stats['light'] = [
        'min' => $row['min'],
        'max' => $row['max'],
        'avg' => $row['avg']
    ];

    // Soil statistics
    $query = "SELECT MIN(soil) as min, MAX(soil) as max, AVG(soil) as avg 
              FROM sensor_soil 
              WHERE $date_condition";
    $res = $conn->query($query);
    $row = $res->fetch_assoc();
    $stats['soil'] = [
        'min' => $row['min'],
        'max' => $row['max'],
        'avg' => $row['avg']
    ];

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'period' => $period
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}
