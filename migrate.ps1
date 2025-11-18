# Migration Script - Greenhouse Dashboard v3.0
# PowerShell Script to update all files

Write-Host "🌿 Greenhouse Dashboard - Migration to v3.0" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""

$rootPath = "c:\laragon\www\IoT_GreenHouse"

# Check if directory exists
if (-not (Test-Path $rootPath)) {
    Write-Host "❌ Error: Directory not found: $rootPath" -ForegroundColor Red
    exit 1
}

Set-Location $rootPath

Write-Host "📁 Current directory: $rootPath" -ForegroundColor Cyan
Write-Host ""

# Backup old files
Write-Host "💾 Step 1: Creating backups..." -ForegroundColor Yellow
$backupDir = "backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

Copy-Item "index.php" "$backupDir/index.php" -ErrorAction SilentlyContinue
Copy-Item "assets/css/style.css" "$backupDir/style.css" -ErrorAction SilentlyContinue
Copy-Item "assets/js/app.js" "$backupDir/app.js" -ErrorAction SilentlyContinue
Copy-Item "api/get_data.php" "$backupDir/get_data.php" -ErrorAction SilentlyContinue
Copy-Item "api/clear_logs.php" "$backupDir/clear_logs.php" -ErrorAction SilentlyContinue

Write-Host "✅ Backup created in: $backupDir" -ForegroundColor Green
Write-Host ""

# Update main files
Write-Host "📝 Step 2: Updating main files..." -ForegroundColor Yellow

if (Test-Path "index_new.php") {
    Copy-Item "index_new.php" "index.php" -Force
    Write-Host "  ✓ index.php updated" -ForegroundColor Green
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

# Update API files
Write-Host "🔌 Step 3: Updating API files..." -ForegroundColor Yellow

if (Test-Path "api/get_data_new.php") {
    Copy-Item "api/get_data_new.php" "api/get_data.php" -Force
    Write-Host "  ✓ get_data.php updated" -ForegroundColor Green
}

if (Test-Path "api/clear_logs_new.php") {
    Copy-Item "api/clear_logs_new.php" "api/clear_logs.php" -Force
    Write-Host "  ✓ clear_logs.php updated" -ForegroundColor Green
}

Write-Host ""

# Check Arduino sketches
Write-Host "🔧 Step 4: Arduino sketches status..." -ForegroundColor Yellow

if (Test-Path "sketch_nov11a_new.ino") {
    Write-Host "  ℹ️  ESP1 sketch ready: sketch_nov11a_new.ino" -ForegroundColor Cyan
    Write-Host "     Please upload manually to ESP8266 #1" -ForegroundColor Gray
}

if (Test-Path "sketch_nov11b_new.ino") {
    Write-Host "  ℹ️  ESP2 sketch ready: sketch_nov11b_new.ino" -ForegroundColor Cyan
    Write-Host "     Please upload manually to ESP8266 #2" -ForegroundColor Gray
}

Write-Host ""

# Database migration reminder
Write-Host "💾 Step 5: Database migration..." -ForegroundColor Yellow
Write-Host "  ⚠️  IMPORTANT: Run the SQL script!" -ForegroundColor Red
Write-Host "     File: database/schema_update.sql" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Option 1: Using MySQL command line:" -ForegroundColor Gray
Write-Host "     mysql -u root -p your_database < database/schema_update.sql" -ForegroundColor White
Write-Host ""
Write-Host "  Option 2: Using phpMyAdmin:" -ForegroundColor Gray
Write-Host "     1. Open phpMyAdmin" -ForegroundColor White
Write-Host "     2. Select your database" -ForegroundColor White
Write-Host "     3. Go to 'Import' tab" -ForegroundColor White
Write-Host "     4. Choose file: database/schema_update.sql" -ForegroundColor White
Write-Host "     5. Click 'Go'" -ForegroundColor White
Write-Host ""

# Final summary
Write-Host "=============================================" -ForegroundColor Green
Write-Host "✅ Migration completed!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next steps:" -ForegroundColor Cyan
Write-Host "  1. ✓ Files backed up to: $backupDir" -ForegroundColor White
Write-Host "  2. ✓ PHP/CSS/JS files updated" -ForegroundColor White
Write-Host "  3. ⏳ Run database/schema_update.sql" -ForegroundColor Yellow
Write-Host "  4. ⏳ Upload Arduino sketches to ESP8266 devices" -ForegroundColor Yellow
Write-Host "  5. ⏳ Update WiFi credentials in sketches" -ForegroundColor Yellow
Write-Host "  6. ⏳ Test dashboard in browser" -ForegroundColor Yellow
Write-Host ""
Write-Host "🌐 Access dashboard at: http://localhost/IoT_GreenHouse/" -ForegroundColor Cyan
Write-Host ""
Write-Host "📖 For detailed guide, see: README_ENHANCED.md" -ForegroundColor Gray
Write-Host ""
