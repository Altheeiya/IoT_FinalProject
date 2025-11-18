# 🌿 IoT Greenhouse Monitoring System v3.0

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![Arduino](https://img.shields.io/badge/Arduino-ESP8266-00979D)](https://www.arduino.cc/)

Smart Greenhouse Monitoring & Control System dengan dashboard real-time, automatic control, dan data analytics.

![Dashboard Preview](https://via.placeholder.com/800x400/10b981/ffffff?text=Greenhouse+Dashboard+v3.0)

## ✨ Features

### 🎯 Core Features

- ✅ **Real-time Monitoring** - Monitor suhu, kelembapan, cahaya, dan kelembapan tanah secara real-time
- ✅ **Auto/Manual Mode** - Kontrol otomatis berdasarkan threshold atau manual control
- ✅ **Alert System** - Notifikasi otomatis jika nilai sensor abnormal
- ✅ **Data Export** - Export data historis ke CSV untuk analisis di Excel
- ✅ **Statistics** - Statistik Min/Max/Avg harian, mingguan, dan bulanan
- ✅ **Dark/Light Mode** - Tema gelap dan terang dengan transisi smooth

### 📊 Dashboard Features

- 📈 **4 Separate Charts** - Grafik terpisah untuk Temperature, Humidity, Light, dan Soil
- 🎛️ **Actuator Control** - Kontrol pompa, kipas, dan lampu
- 📅 **Date Range Filter** - Filter data historis berdasarkan tanggal
- 🔔 **Connection Status** - Monitor status online/offline ESP8266
- ⚙️ **Threshold Settings** - Set batas min/max untuk setiap sensor
- 📱 **Responsive Design** - Optimized untuk desktop, tablet, dan mobile

## 🚀 Quick Start

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Apache/Nginx web server
- Arduino IDE (for ESP8266)
- ESP8266 boards (NodeMCU recommended)

### Installation

1. **Clone repository**

   ```bash
   git clone https://github.com/edfazmi/IoT_GreenHouse.git
   cd IoT_GreenHouse
   ```

2. **Setup Database**

   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE greenhouse_db"

   # Import schema
   mysql -u root -p greenhouse_db < database/schema_update.sql
   ```

3. **Configure Database Connection**

   ```bash
   cp config/db.example.php config/db.php
   # Edit db.php with your credentials
   ```

4. **Setup Web Server**

   For XAMPP/Laragon:

   - Copy project to `htdocs` or `www` folder
   - Access: `http://localhost/IoT_GreenHouse/`

   For production server:

   - Configure virtual host
   - Set document root to project folder

5. **Upload Arduino Sketches**
   - ESP1 (DHT22 + LDR): Upload `sketch_nov11a_new.ino`
   - ESP2 (Soil Sensor): Upload `sketch_nov11b_new.ino`
   - Update WiFi credentials and server IP in sketches

## 📁 Project Structure

```
IoT_GreenHouse/
├── api/                      # API endpoints
│   ├── get_data.php         # Get sensor data
│   ├── get_thresholds.php   # Get threshold settings
│   ├── save_thresholds.php  # Save thresholds
│   ├── set_mode.php         # Set auto/manual mode
│   ├── get_statistics.php   # Get statistics
│   ├── export_data.php      # Export to CSV
│   ├── heartbeat.php        # Device heartbeat
│   └── ...
├── assets/
│   ├── css/
│   │   └── style.css        # Stylesheet
│   └── js/
│       └── app.js           # Main JavaScript
├── config/
│   ├── db.php               # Database config (gitignored)
│   └── db.example.php       # Database config template
├── database/
│   └── schema_update.sql    # Database schema
├── sketch_nov11a_new.ino    # ESP8266 #1 code
├── sketch_nov11b_new.ino    # ESP8266 #2 code
└── index.php                # Main dashboard
```

## 🔧 Configuration

### Database Configuration

Edit `config/db.php`:

```php
$host = 'localhost';
$user = 'your_user';
$pass = 'your_password';
$db   = 'greenhouse_db';
```

### Arduino Configuration

Edit sketches before uploading:

```cpp
const char* ssid = "Your_WiFi_SSID";
const char* password = "Your_WiFi_Password";
const char* server = "http://YOUR_SERVER_IP:PORT/api";
```

## 🎮 Usage

### Dashboard Access

```
http://localhost/IoT_GreenHouse/
```

### Set Thresholds

1. Click "⚙️ Threshold Settings"
2. Set Min/Max values for each sensor
3. Click "Save Settings"

### Enable Auto Mode

1. Click "MANUAL" toggle to switch to "AUTO"
2. System will automatically control actuators based on thresholds

### Export Data

1. Optional: Select date range
2. Click "📥 Export Data"
3. CSV file will download

### View Statistics

1. Select period: Today/Week/Month
2. View Min/Avg/Max for all sensors

## 📊 Hardware Setup

### ESP8266 #1 (Main Sensors)

- **DHT22** → Pin D4
- **LDR** → Pin A0
- **Relay 1 (Fan)** → Pin D1
- **Relay 2 (Light)** → Pin D2

### ESP8266 #2 (Soil Sensor)

- **Soil Moisture Sensor** → Pin A0
- **Relay 1 (Pump)** → Pin D1

## 🌐 API Endpoints

| Endpoint                       | Method | Description                  |
| ------------------------------ | ------ | ---------------------------- |
| `/api/get_data.php`            | GET    | Get all sensor data & status |
| `/api/get_thresholds.php`      | GET    | Get threshold settings       |
| `/api/save_thresholds.php`     | POST   | Save threshold settings      |
| `/api/set_mode.php`            | POST   | Set auto/manual mode         |
| `/api/get_statistics.php`      | GET    | Get sensor statistics        |
| `/api/export_data.php`         | GET    | Export data to CSV           |
| `/api/heartbeat.php`           | POST   | Device heartbeat             |
| `/api/get_actuator_status.php` | GET    | Get actuator status          |

## 🎨 Screenshots

### Dashboard Overview

![Dashboard](https://via.placeholder.com/800x400/10b981/ffffff?text=Dashboard+Overview)

### Dark Mode

![Dark Mode](https://via.placeholder.com/800x400/1f2937/e5e7eb?text=Dark+Mode)

### Mobile View

![Mobile](https://via.placeholder.com/400x800/10b981/ffffff?text=Mobile+Responsive)

## 🛠️ Troubleshooting

### Error 404 - API Not Found

```bash
# Run quick fix script
.\fix_404.ps1

# Or test API
http://localhost/IoT_GreenHouse/test_dashboard.html
```

See [TROUBLESHOOTING_404.md](TROUBLESHOOTING_404.md) for detailed guide.

### ESP8266 Not Connecting

1. Check WiFi credentials
2. Check server IP address
3. Monitor serial output (115200 baud)
4. Ensure firewall allows connection

### Database Connection Error

1. Verify MySQL is running
2. Check credentials in `config/db.php`
3. Ensure database exists
4. Import `schema_update.sql`

## 📚 Documentation

- [Installation Guide (ID)](INSTALL_ID.md) - Panduan instalasi lengkap
- [Feature Comparison](COMPARISON.md) - Perbandingan versi lama vs baru
- [Layout Guide](LAYOUT_GUIDE.md) - Design specifications
- [Troubleshooting](TROUBLESHOOTING_404.md) - Common issues & solutions

## 🔒 Security Notes

⚠️ **IMPORTANT**: Before deploying to production:

1. Change default database credentials
2. Use prepared statements (already implemented)
3. Enable HTTPS
4. Set proper file permissions
5. Don't commit `config/db.php` to git (already in .gitignore)
6. Use strong passwords for database
7. Implement authentication if exposing to internet

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**edfazmi**

- GitHub: [@edfazmi](https://github.com/edfazmi)

## 🙏 Acknowledgments

- Chart.js for beautiful charts
- Tailwind CSS for styling
- Flatpickr for date picker
- Lucide Icons for icons

## 📈 Version History

- **v3.0** (2025-01-18)

  - Complete dashboard redesign
  - Added auto/manual mode
  - Added threshold settings
  - Added statistics & analytics
  - Added data export
  - Complete dark mode
  - 4 separate charts
  - Connection monitoring
  - Alert system

- **v2.0** (Previous)
  - Basic monitoring
  - Manual control
  - 3 charts

## 🔮 Future Plans

- [ ] Push notifications
- [ ] Email alerts
- [ ] Mobile app
- [ ] Multi-user authentication
- [ ] Machine learning predictions
- [ ] Weather API integration
- [ ] Camera monitoring

---

⭐ **If you find this project useful, please give it a star!** ⭐

Made with ❤️ for smart agriculture
