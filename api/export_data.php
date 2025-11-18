<?php
require_once __DIR__ . '/../config/db.php';

try {
    // Get date filter parameters
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=greenhouse_data_' . date('Y-m-d_His') . '.csv');

    // Create output stream
    $output = fopen('php://output', 'w');

    // BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Write headers
    fputcsv($output, ['Timestamp', 'Temperature (°C)', 'Humidity (%)', 'Light (lx)', 'Soil Moisture (%)']);

    // Get all sensor data within date range
    $query = "SELECT 
                s1.waktu as timestamp,
                s1.suhu as temperature,
                s1.kelembapan as humidity,
                s2.nilai_ldr as light,
                s3.soil as soil
              FROM sensor_suhu_kelembapan s1
              LEFT JOIN sensor_ldr s2 ON DATE(s1.waktu) = DATE(s2.waktu) 
                AND TIME(s1.waktu) = TIME(s2.waktu)
              LEFT JOIN sensor_soil s3 ON DATE(s1.waktu) = DATE(s3.waktu) 
                AND TIME(s1.waktu) = TIME(s3.waktu)
              WHERE s1.waktu BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
              ORDER BY s1.waktu DESC
              LIMIT 10000";

    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['timestamp'],
            $row['temperature'],
            $row['humidity'],
            $row['light'],
            $row['soil']
        ]);
    }

    fclose($output);
    exit;
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'msg' => $e->getMessage()
    ]);
}
