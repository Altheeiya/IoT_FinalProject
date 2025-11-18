# Quick Fix Script untuk Error 404 API
# Jalankan di PowerShell

Write-Host "🔧 Greenhouse Dashboard - Quick Fix 404" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

$root = "c:\laragon\www\IoT_GreenHouse"

if (-not (Test-Path $root)) {
    Write-Host "❌ Error: Folder tidak ditemukan: $root" -ForegroundColor Red
    exit 1
}

Set-Location $root

Write-Host "📂 Working directory: $root" -ForegroundColor Cyan
Write-Host ""

# Step 1: Copy main files
Write-Host "📋 Step 1: Update main files..." -ForegroundColor Yellow

if (Test-Path "index_new.php") {
    Copy-Item "index_new.php" "index.php" -Force
    Write-Host "  ✓ index.php updated" -ForegroundColor Green
}
else {
    Write-Host "  ⚠ index_new.php not found" -ForegroundColor Yellow
}

if (Test-Path "assets/css/style_new.css") {
    Copy-Item "assets/css/style_new.css" "assets/css/style.css" -Force
    Write-Host "  ✓ style.css updated" -ForegroundColor Green
}

if (Test-Path "assets/js/app_new.js") {
    Copy-Item "assets/js/app_new.js" "assets/js/app.js" -Force
    Write-Host "  ✓ app.js updated" -ForegroundColor Green
}

Write-Host ""

# Step 2: Copy API files
Write-Host "🔌 Step 2: Update API files..." -ForegroundColor Yellow

if (Test-Path "api/get_data_new.php") {
    Copy-Item "api/get_data_new.php" "api/get_data.php" -Force
    Write-Host "  ✓ get_data.php updated" -ForegroundColor Green
}

if (Test-Path "api/clear_logs_new.php") {
    Copy-Item "api/clear_logs_new.php" "api/clear_logs.php" -Force
    Write-Host "  ✓ clear_logs.php updated" -ForegroundColor Green
}

Write-Host ""

# Step 3: Verify API files
Write-Host "✅ Step 3: Verifying API files..." -ForegroundColor Yellow

$apiFiles = @(
    "get_data.php",
    "get_thresholds.php",
    "save_thresholds.php",
    "set_mode.php",
    "get_statistics.php",
    "export_data.php",
    "clear_logs.php",
    "heartbeat.php",
    "get_actuator_status.php"
)

$allOk = $true

foreach ($file in $apiFiles) {
    $path = "api/$file"
    if (Test-Path $path) {
        Write-Host "  ✓ $file" -ForegroundColor Green
    }
    else {
        Write-Host "  ✗ $file NOT FOUND!" -ForegroundColor Red
        $allOk = $false
    }
}

Write-Host ""

# Step 4: Test URLs
Write-Host "🌐 Step 4: Testing URLs..." -ForegroundColor Yellow

$testUrls = @(
    "http://localhost/IoT_GreenHouse/",
    "http://localhost/IoT_GreenHouse/api/test_api.php",
    "http://localhost/IoT_GreenHouse/test_dashboard.html"
)

foreach ($url in $testUrls) {
    Write-Host "  Testing: $url" -ForegroundColor Gray
    try {
        $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 5 -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            Write-Host "    ✓ OK (200)" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "    ✗ FAILED: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Green

if ($allOk) {
    Write-Host "✅ All API files are present!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📝 Next steps:" -ForegroundColor Cyan
    Write-Host "  1. Pastikan Laragon Apache & MySQL running" -ForegroundColor White
    Write-Host "  2. Import database/schema_update.sql ke database" -ForegroundColor White
    Write-Host "  3. Buka: http://localhost/IoT_GreenHouse/" -ForegroundColor White
    Write-Host "  4. Test API: http://localhost/IoT_GreenHouse/test_dashboard.html" -ForegroundColor White
    Write-Host ""
    Write-Host "🔍 Jika masih error 404:" -ForegroundColor Yellow
    Write-Host "  - Buka browser DevTools (F12)" -ForegroundColor Gray
    Write-Host "  - Lihat tab Network" -ForegroundColor Gray
    Write-Host "  - Check file mana yang 404" -ForegroundColor Gray
    Write-Host "  - Lihat TROUBLESHOOTING_404.md" -ForegroundColor Gray
}
else {
    Write-Host "⚠️  Some API files are missing!" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Please check:" -ForegroundColor White
    Write-Host "  - File *_new.php harus ada untuk di-copy" -ForegroundColor Gray
    Write-Host "  - Atau buat manual dari template" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Press any key to continue..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
