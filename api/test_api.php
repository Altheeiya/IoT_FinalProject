<?php
// Test file untuk mengecek koneksi API
header('Content-Type: application/json');

$apiFiles = [
    'get_data.php',
    'get_thresholds.php',
    'save_thresholds.php',
    'set_mode.php',
    'get_statistics.php',
    'export_data.php',
    'clear_logs.php',
    'heartbeat.php',
    'get_actuator_status.php'
];

$results = [];

foreach ($apiFiles as $file) {
    $path = __DIR__ . '/' . $file;
    $results[$file] = [
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'path' => $path
    ];
}

echo json_encode([
    'success' => true,
    'api_files' => $results,
    'current_dir' => __DIR__,
    'server_info' => [
        'PHP_SELF' => $_SERVER['PHP_SELF'] ?? 'N/A',
        'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A'
    ]
], JSON_PRETTY_PRINT);
