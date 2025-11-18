# 🔧 Troubleshooting Error 404 - API Not Found

## ✅ SOLUSI SUDAH DITERAPKAN

File API sudah di-copy dan siap digunakan:

- ✅ `api/get_data.php`
- ✅ `api/get_thresholds.php`
- ✅ `api/save_thresholds.php`
- ✅ `api/set_mode.php`
- ✅ `api/get_statistics.php`
- ✅ `api/export_data.php`
- ✅ `api/clear_logs.php`
- ✅ `api/heartbeat.php`
- ✅ `api/get_actuator_status.php`

## 🧪 CARA TEST

### 1. Test Manual File API

Buka browser dan akses URL berikut:

```
http://localhost/IoT_GreenHouse/api/test_api.php
```

Atau gunakan test dashboard:

```
http://localhost/IoT_GreenHouse/test_dashboard.html
```

### 2. Test Langsung API Endpoint

```
http://localhost/IoT_GreenHouse/api/get_data.php
http://localhost/IoT_GreenHouse/api/get_thresholds.php
http://localhost/IoT_GreenHouse/api/get_statistics.php?period=today
```

### 3. Test Dashboard Utama

```
http://localhost/IoT_GreenHouse/
```

## 🔍 DIAGNOSA MASALAH 404

### Kemungkinan Penyebab:

#### 1. File Tidak Ada

**Cek:**

```powershell
Get-ChildItem c:\laragon\www\IoT_GreenHouse\api\*.php
```

**Solusi:**

```powershell
cd c:\laragon\www\IoT_GreenHouse\api
Copy-Item get_data_new.php get_data.php -Force
Copy-Item clear_logs_new.php clear_logs.php -Force
```

#### 2. Path Salah di JavaScript

**Cek file:** `assets/js/app.js`

Pastikan path API seperti ini:

```javascript
fetch("api/get_data.php"); // ✅ Benar
fetch("/api/get_data.php"); // ❌ Salah jika tidak di root
fetch("get_data.php"); // ❌ Salah, tidak ada folder api
```

#### 3. Laragon Tidak Jalan

**Cek:**

- Buka Laragon Control Panel
- Pastikan Apache STARTED (hijau)
- Pastikan MySQL STARTED (hijau)

**Solusi:**

```
Klik "Start All" di Laragon
```

#### 4. Virtual Host Salah

**Cek akses:**

```
http://localhost/IoT_GreenHouse/     ✅ Harus bisa
http://iot_greenhouse.test/          ⚠️ Perlu konfigurasi vhost
```

#### 5. Permission Denied

**Solusi:**

```powershell
# Berikan full permission ke folder
icacls "c:\laragon\www\IoT_GreenHouse" /grant Everyone:F /T
```

#### 6. PHP Syntax Error

**Cek PHP Error Log:**

```
c:\laragon\etc\apache2\logs\error.log
```

**Test PHP:**

```powershell
cd c:\laragon\www\IoT_GreenHouse\api
php -l get_data.php
```

#### 7. Database Belum Diimport

**Solusi:**

1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database Anda
3. Import: `database/schema_update.sql`

#### 8. File Config Database Salah

**Cek:** `config/db.php`

Pastikan isi seperti ini:

```php
<?php
$host = 'localhost';
$user = 'root';
$pass = '';  // atau password MySQL Anda
$db   = 'nama_database_anda';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

## 🛠️ QUICK FIX COMMANDS

Jalankan command ini di PowerShell untuk fix semua:

```powershell
# Masuk ke folder
cd c:\laragon\www\IoT_GreenHouse

# Copy semua file yang diperlukan
Copy-Item index_new.php index.php -Force
Copy-Item assets\css\style_new.css assets\css\style.css -Force
Copy-Item assets\js\app_new.js assets\js\app.js -Force
Copy-Item api\get_data_new.php api\get_data.php -Force
Copy-Item api\clear_logs_new.php api\clear_logs.php -Force

# Verifikasi file ada
Test-Path api\get_data.php
Test-Path api\get_thresholds.php
Test-Path api\set_mode.php

# Restart Laragon
# (Manual: Klik "Stop All" lalu "Start All")
```

## 📱 TEST DARI BROWSER

### Chrome DevTools (F12)

1. Buka dashboard: `http://localhost/IoT_GreenHouse/`
2. Tekan F12
3. Klik tab "Network"
4. Refresh halaman (F5)
5. Lihat request ke API:
   - ✅ Status 200 = Sukses
   - ❌ Status 404 = File tidak ditemukan
   - ❌ Status 500 = PHP error

### Console Errors

Di tab "Console" lihat error:

```
Failed to fetch           → API tidak bisa diakses
404 Not Found            → File tidak ada
CORS error               → Masalah permission
Syntax error             → JavaScript error
```

## ✅ VERIFIKASI SUKSES

Jika semua OK, Anda akan lihat:

1. **Network Tab:**

   - ✅ `get_data.php` → Status 200
   - ✅ `get_thresholds.php` → Status 200
   - ✅ `get_statistics.php` → Status 200

2. **Dashboard:**

   - ✅ Sensor cards menampilkan nilai
   - ✅ Charts muncul
   - ✅ Log aktivitas terisi
   - ✅ Tidak ada error di console

3. **Connection Status:**
   - ✅ Indicator hijau/kuning/merah (bukan "Checking...")

## 🆘 MASIH ERROR?

### Langkah Debugging:

1. **Test file test_api.php:**

   ```
   http://localhost/IoT_GreenHouse/api/test_api.php
   ```

   Harus return JSON dengan daftar file

2. **Test langsung get_data.php:**

   ```
   http://localhost/IoT_GreenHouse/api/get_data.php
   ```

   Harus return JSON dengan data sensor

3. **Check Apache error log:**

   ```
   c:\laragon\etc\apache2\logs\error.log
   ```

4. **Enable PHP errors:**
   Edit `c:\laragon\etc\php\php.ini`

   ```ini
   display_errors = On
   error_reporting = E_ALL
   ```

   Restart Laragon

5. **Test dengan curl:**
   ```powershell
   curl http://localhost/IoT_GreenHouse/api/get_data.php
   ```

## 📞 Informasi Detail Error

Jika masih error, capture informasi ini:

1. Screenshot error di browser
2. Output dari test_api.php
3. Error dari console browser (F12)
4. Isi file error.log
5. Hasil command: `Test-Path api\*.php`

---

**File test tersedia:**

- `test_dashboard.html` - Visual API tester
- `api/test_api.php` - API file checker

**Akses:**

```
http://localhost/IoT_GreenHouse/test_dashboard.html
http://localhost/IoT_GreenHouse/api/test_api.php
```
