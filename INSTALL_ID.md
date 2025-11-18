# 🌿 Panduan Instalasi Cepat - Dashboard Greenhouse v3.0

## 🚀 Cara Install (Mudah!)

### Metode 1: Otomatis (Recommended)

1. **Buka PowerShell** di folder `c:\laragon\www\IoT_GreenHouse`

   ```powershell
   cd c:\laragon\www\IoT_GreenHouse
   ```

2. **Jalankan script migrasi**

   ```powershell
   .\migrate.ps1
   ```

3. **Import Database**

   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Pilih database greenhouse Anda
   - Klik tab "Import"
   - Pilih file: `database/schema_update.sql`
   - Klik "Go"

4. **Selesai!** Buka browser dan akses:
   ```
   http://localhost/IoT_GreenHouse/
   ```

### Metode 2: Manual

1. **Backup File Lama**

   - Copy `index.php` → `index_backup.php`
   - Copy `assets/css/style.css` → `style_backup.css`
   - Copy `assets/js/app.js` → `app_backup.js`

2. **Update File Utama**

   ```
   Rename: index_new.php → index.php
   Rename: assets/css/style_new.css → assets/css/style.css
   Rename: assets/js/app_new.js → assets/js/app.js
   ```

3. **Update File API**

   ```
   Rename: api/get_data_new.php → api/get_data.php
   Rename: api/clear_logs_new.php → api/clear_logs.php
   ```

4. **Import Database** (sama seperti Metode 1)

5. **Upload Sketch Arduino**
   - ESP8266 #1: Upload `sketch_nov11a_new.ino`
   - ESP8266 #2: Upload `sketch_nov11b_new.ino`

## ⚙️ Konfigurasi Arduino

Edit di sketch sebelum upload:

```cpp
// WiFi Settings
const char* ssid = "NAMA_WIFI_ANDA";
const char* password = "PASSWORD_WIFI";

// Server Settings
const char* server = "http://IP_KOMPUTER_ANDA:PORT/api";
// Contoh: "http://192.168.1.100:8000/api"
```

**Cara mendapatkan IP komputer:**

```powershell
ipconfig
```

Cari "IPv4 Address"

## ✅ Checklist Instalasi

- [ ] File PHP/CSS/JS sudah di-update
- [ ] Database schema_update.sql sudah diimport
- [ ] Tidak ada error di phpMyAdmin
- [ ] WiFi credentials di sketch sudah benar
- [ ] Server IP di sketch sudah benar
- [ ] Sketch uploaded ke ESP8266 #1
- [ ] Sketch uploaded ke ESP8266 #2
- [ ] Dashboard bisa dibuka di browser
- [ ] Connection status menunjukkan "Online"
- [ ] Chart muncul dengan benar
- [ ] Dark mode berfungsi

## 🔍 Verifikasi

### 1. Test Database

Buka phpMyAdmin dan check tabel baru:

- `sensor_thresholds`
- `system_settings`
- `device_status`
- `alert_history`
- `sensor_statistics`

### 2. Test Dashboard

- Buka: `http://localhost/IoT_GreenHouse/`
- Semua card sensor harus tampil
- 4 chart harus muncul
- Toggle dark mode harus bekerja

### 3. Test ESP8266

Monitor Serial Arduino:

- "Terhubung ke WiFi!" harus muncul
- "Heartbeat sent: 200" harus muncul tiap 5 detik
- "Sensor data sent: 200" harus muncul
- Connection status di dashboard: "All Online"

## 🐛 Troubleshooting

### Chart tidak muncul

```
Solusi:
1. Clear cache browser (Ctrl + F5)
2. Check console browser (F12)
3. Pastikan Chart.js loaded
```

### ESP tidak online

```
Solusi:
1. Check serial monitor Arduino
2. Pastikan WiFi credentials benar
3. Pastikan server IP benar
4. Ping komputer dari HP di WiFi yang sama
5. Check firewall tidak block port
```

### Error di database

```
Solusi:
1. Drop semua tabel baru manual
2. Import ulang schema_update.sql
3. Refresh dashboard
```

### Dark mode tidak sempurna

```
Solusi:
1. Clear browser cache
2. Hard refresh (Ctrl + Shift + R)
3. Check file style.css sudah terupdate
```

## 📱 Testing di HP

1. Cari IP komputer: `ipconfig`
2. Di HP, sambung ke WiFi yang sama
3. Buka browser HP
4. Akses: `http://IP_KOMPUTER:8000/IoT_GreenHouse/`
5. Dashboard harus responsive

## 🎯 Fitur yang Bisa Langsung Dicoba

1. **Threshold Settings**

   - Klik tombol "⚙️ Threshold Settings"
   - Set nilai Min/Max untuk setiap sensor
   - Klik "Save Settings"

2. **Mode Auto**

   - Klik toggle "MANUAL" jadi "AUTO"
   - Aktuator akan otomatis ON/OFF sesuai threshold

3. **Export Data**

   - Klik "📥 Export Data"
   - File CSV akan otomatis terdownload
   - Buka dengan Excel

4. **Date Filter**

   - Klik date range picker
   - Pilih tanggal awal dan akhir
   - Chart akan update sesuai range

5. **Statistics**

   - Pilih period: Today/Week/Month
   - Lihat Min/Avg/Max setiap sensor

6. **Dark Mode**
   - Klik icon 🌙
   - Semua elemen berubah tema
   - Setting tersimpan otomatis

## 📞 Bantuan

Jika ada masalah:

1. Lihat README_ENHANCED.md untuk detail lengkap
2. Check browser console (F12) untuk JavaScript error
3. Check serial monitor Arduino untuk hardware error
4. Pastikan semua file terupdate dengan benar

## 🎉 Selamat!

Dashboard v3.0 sudah siap digunakan dengan fitur lengkap:
✅ Alert System
✅ Auto Mode
✅ Statistics
✅ Export Data
✅ Dark Mode
✅ Connection Status
✅ Dan masih banyak lagi!

Enjoy! 🌿
