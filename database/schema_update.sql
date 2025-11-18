-- Schema Update untuk fitur-fitur tambahan IoT Greenhouse
-- Jalankan SQL ini untuk menambahkan tabel dan field baru

-- 1. Tabel untuk threshold/batas sensor
CREATE TABLE IF NOT EXISTS sensor_thresholds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_type VARCHAR(50) NOT NULL UNIQUE,
    min_value FLOAT DEFAULT NULL,
    max_value FLOAT DEFAULT NULL,
    actuator_code VARCHAR(50) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default thresholds
INSERT INTO sensor_thresholds (sensor_type, min_value, max_value, actuator_code) VALUES
('temperature', 20.0, 35.0, 'fan'),
('humidity', 40.0, 80.0, NULL),
('light', 300, 1000, 'light'),
('soil', 30.0, 70.0, 'pump')
ON DUPLICATE KEY UPDATE sensor_type=sensor_type;

-- 2. Tabel untuk system settings
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO system_settings (setting_key, setting_value) VALUES
('auto_mode', '0'),
('alert_enabled', '1'),
('refresh_interval', '5000')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- 3. Tabel untuk device status (heartbeat)
CREATE TABLE IF NOT EXISTS device_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_code VARCHAR(50) NOT NULL UNIQUE,
    device_name VARCHAR(100),
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_online TINYINT(1) DEFAULT 1,
    ip_address VARCHAR(45) DEFAULT NULL
);

-- Insert default devices
INSERT INTO device_status (device_code, device_name, is_online) VALUES
('ESP1', 'ESP8266 - Sensor Utama', 0),
('ESP2', 'ESP8266 - Sensor Tanah', 0)
ON DUPLICATE KEY UPDATE device_code=device_code;

-- 4. Tabel untuk alert history
CREATE TABLE IF NOT EXISTS alert_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_type VARCHAR(50) NOT NULL,
    alert_type VARCHAR(20) NOT NULL,
    sensor_value FLOAT NOT NULL,
    threshold_value FLOAT NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0
);

-- 5. Tabel untuk statistics cache (optional - untuk performa)
CREATE TABLE IF NOT EXISTS sensor_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_type VARCHAR(50) NOT NULL,
    stat_date DATE NOT NULL,
    min_value FLOAT,
    max_value FLOAT,
    avg_value FLOAT,
    count_readings INT DEFAULT 0,
    UNIQUE KEY unique_sensor_date (sensor_type, stat_date)
);

-- 6. Update actuator table - tambah kolom auto_mode jika belum ada
ALTER TABLE actuator 
ADD COLUMN IF NOT EXISTS auto_enabled TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS last_auto_trigger TIMESTAMP NULL DEFAULT NULL;

-- 7. Index untuk performa
CREATE INDEX idx_sensor_suhu_waktu ON sensor_suhu_kelembapan(waktu);
CREATE INDEX idx_sensor_ldr_waktu ON sensor_ldr(waktu);
CREATE INDEX idx_sensor_soil_waktu ON sensor_soil(waktu);
CREATE INDEX idx_log_created ON log_aktivitas(created_at);
CREATE INDEX idx_alert_created ON alert_history(created_at);
