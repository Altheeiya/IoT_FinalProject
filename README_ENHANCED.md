# 🌿 Greenhouse IoT Dashboard - Enhanced Version 3.0

## 📋 Fitur Lengkap yang Sudah Diimplementasikan

### ✅ 1. Dashboard Overview

- Ringkasan status semua sensor dalam satu tampilan
- Indikator jumlah aktuator yang aktif
- Mode sistem (Manual/Auto) yang jelas terlihat

### ✅ 2. Alert/Notifikasi System

- Peringatan otomatis jika nilai sensor di luar batas normal
- Alert visual pada card sensor
- Pop-up notification untuk alert kritis
- Badge warning pada sensor yang bermasalah

### ✅ 3. Threshold Settings

- Interface untuk mengatur batas atas/bawah setiap sensor
- Modal dialog yang user-friendly
- Penyimpanan threshold ke database
- Auto-trigger aktuator berdasarkan threshold (dalam mode AUTO)

### ✅ 4. Mode Manual/Auto

- Toggle switch antara kontrol manual dan otomatis
- Mode Manual: User mengontrol aktuator secara manual
- Mode Auto: Sistem otomatis mengontrol berdasarkan threshold
- Indikator visual yang jelas untuk mode aktif

### ✅ 5. Export Data

- Download history data dalam format CSV
- Compatible dengan Excel
- Filter berdasarkan date range
- Include semua data sensor (Temperature, Humidity, Light, Soil)

### ✅ 6. Status Koneksi

- Indikator online/offline untuk setiap ESP8266
- Heartbeat mechanism (5 detik interval)
- Visual indicator (Green = Online, Yellow = Partial, Red = Offline)
- Auto-detect device yang tidak mengirim heartbeat

### ✅ 7. Time Range Filter

- Date picker untuk memilih range tanggal
- Filter history chart berdasarkan tanggal
- Reset filter dengan satu klik
- Menggunakan Flatpickr untuk UI yang bagus

### ✅ 8. Average/Min/Max Statistics

- Statistik harian, mingguan, dan bulanan
- Menampilkan nilai Min, Average, dan Max untuk setiap sensor
- Selector untuk memilih periode (Today/Week/Month)
- Update real-time

### ✅ 9. Dark/Light Mode

- Toggle tema yang smooth
- Semua elemen berubah (cards, charts, modals, inputs)
- Chart themes ikut berubah
- Tersimpan di localStorage
- Transition yang smooth untuk semua elemen

### ✅ 10. Responsive Cards

- Setiap sensor memiliki card dengan icon yang menarik
- Gradient background yang berbeda tiap sensor
- Hover effects
- Fully responsive untuk mobile, tablet, dan desktop

### ✅ 11. Grafik Terpisah Per Sensor

- 4 Chart terpisah: Temperature, Humidity, Light, Soil Moisture
- Masing-masing chart dengan warna dan konfigurasi yang sesuai
- Temperature: Orange (°C)
- Humidity: Blue (%)
- Light: Yellow (lx)
- Soil Moisture: Green (%)
- Chart.js dengan konfigurasi optimal

### ✅ 12. Integrasi Hardware-Web

- Arduino sketch updated dengan heartbeat system
- ESP1 (DHT22 + LDR) → Temperature, Humidity, Light
- ESP2 (Soil Sensor) → Soil Moisture
- Auto-sync actuator status dari database
- Polling interval yang efisien (5 detik)

## 📁 File Structure

```
IoT_GreenHouse/
├── database/
│   └── schema_update.sql          # SQL untuk tabel baru
├── api/
│   ├── get_data_new.php           # Enhanced data endpoint
│   ├── get_thresholds.php         # Get threshold settings
│   ├── save_thresholds.php        # Save threshold settings
│   ├── set_mode.php               # Set manual/auto mode
│   ├── get_statistics.php         # Get sensor statistics
│   ├── export_data.php            # Export to CSV
│   ├── clear_logs_new.php         # Clear log history
│   ├── heartbeat.php              # Device heartbeat endpoint
│   └── get_actuator_status.php    # Get actuator status for ESP
├── assets/
│   ├── css/
│   │   └── style_new.css          # Enhanced CSS
│   └── js/
│       └── app_new.js             # Enhanced JavaScript
├── index_new.php                  # Enhanced HTML
├── sketch_nov11a_new.ino          # Updated ESP1 code
└── sketch_nov11b_new.ino          # Updated ESP2 code
```

## 🚀 Instalasi

### 1. Database Setup

```sql
-- Jalankan file SQL ini di database Anda
source database/schema_update.sql;

-- Atau import manual melalui phpMyAdmin
```

### 2. Update File PHP

```bash
# Backup file lama
cp index.php index_old.php
cp api/get_data.php api/get_data_old.php

# Copy file baru
cp index_new.php index.php
cp api/get_data_new.php api/get_data.php
cp api/clear_logs_new.php api/clear_logs.php

# Copy file CSS & JS
cp assets/css/style_new.css assets/css/style.css
cp assets/js/app_new.js assets/js/app.js
```

### 3. Upload Arduino Sketch

- Upload `sketch_nov11a_new.ino` ke ESP8266 #1
- Upload `sketch_nov11b_new.ino` ke ESP8266 #2
- Sesuaikan WiFi credentials dan server IP

### 4. Konfigurasi

Edit di sketch Arduino:

```cpp
const char* ssid = "YourWiFiSSID";
const char* password = "YourPassword";
const char* server = "http://YOUR_SERVER_IP:PORT/api";
```

## 🎨 Proporsi Layout

Layout sudah diperbaiki dengan proporsi yang seimbang:

- Header: Compact dan informatif
- Sensor Cards: Grid 4 kolom (responsive ke 2 dan 1 kolom di mobile)
- Statistics: Full width dengan 4 kolom
- Charts: Grid 2x2 dengan tinggi yang sama
- Controls & Logs: 1:2 ratio

## 🌓 Dark Mode

Dark mode sekarang benar-benar lengkap:

- ✅ Background gradients
- ✅ Card backgrounds
- ✅ Text colors
- ✅ Border colors
- ✅ Chart themes (grid, labels, tooltips)
- ✅ Modal dialogs
- ✅ Input fields
- ✅ Scrollbars
- ✅ Date picker (Flatpickr)

## 📱 Responsive Design

- Desktop (> 1024px): Full 4-column layout
- Tablet (768px - 1024px): 2-column layout
- Mobile (< 768px): Single column, stack layout

## 🔄 Update Path

Jika ingin mengupdate dari versi lama:

1. **Backup database** Anda terlebih dahulu
2. Jalankan `schema_update.sql`
3. Replace file-file dengan versi \_new
4. Test di browser
5. Upload sketch Arduino yang baru

## 🐛 Troubleshooting

### Chart tidak muncul

- Clear browser cache
- Check console untuk error
- Pastikan Chart.js CDN loaded

### Dark mode tidak sempurna

- Hard refresh browser (Ctrl+F5)
- Check localStorage tidak corrupt

### ESP tidak online

- Check WiFi credentials
- Check server IP dan port
- Monitor serial output Arduino
- Pastikan tabel device_status ada

### Threshold tidak bekerja

- Pastikan mode AUTO aktif
- Check threshold values sudah di-set
- Check tabel sensor_thresholds

## 📊 Database Tables Baru

1. **sensor_thresholds**: Menyimpan batas min/max sensor
2. **system_settings**: Pengaturan sistem (auto_mode, dll)
3. **device_status**: Status online/offline ESP8266
4. **alert_history**: History alert yang muncul
5. **sensor_statistics**: Cache statistik (optional)

## 🎯 Testing Checklist

- [ ] Dashboard overview tampil dengan benar
- [ ] Semua 4 chart berfungsi
- [ ] Dark mode switch bekerja sempurna
- [ ] Threshold settings bisa disimpan
- [ ] Mode Manual/Auto bisa di-switch
- [ ] Export CSV berhasil download
- [ ] Date filter bekerja
- [ ] Statistics update sesuai period
- [ ] Connection status menunjukkan online
- [ ] Alert muncul saat sensor di luar threshold
- [ ] Actuator control bekerja di mode manual
- [ ] Auto mode trigger actuator berdasarkan threshold

## 📝 Notes

- Polling interval: 5 detik (bisa diubah di app.js)
- Heartbeat interval: 5 detik (bisa diubah di sketch Arduino)
- Device dianggap offline jika > 30 detik tidak kirim heartbeat
- CSV export limit: 10,000 records
- Chart maximum points: 50 (untuk performa)

## 🆘 Support

Jika ada masalah:

1. Check browser console untuk JavaScript errors
2. Check PHP error logs
3. Monitor Arduino serial output
4. Pastikan semua file API dapat diakses
5. Verify database tables sudah dibuat

## 📈 Future Enhancements

Fitur yang bisa ditambahkan di masa depan:

- Push notifications ke mobile
- Email alerts
- Data visualization yang lebih advanced
- Machine learning untuk prediksi
- Multi-user dengan authentication
- API untuk mobile app
- Historical data aggregation

---

**Version**: 3.0  
**Last Updated**: 2025-01-18  
**Author**: Enhanced by AI Assistant
