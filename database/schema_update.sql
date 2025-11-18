-- ==============================================
-- DATABASE SMARTGREENHOUSE (FINAL WORKING BUILD)
-- ==============================================

CREATE DATABASE IF NOT EXISTS smartgreenhouse;
USE smartgreenhouse;

-- ----------------------------------------------
-- 1. SENSOR SUHU & KELEMBAPAN (ESP1)
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS sensor_suhu_kelembapan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suhu FLOAT NOT NULL,
    kelembapan FLOAT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------
-- 2. SENSOR LDR (ESP1)
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS sensor_ldr (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nilai_ldr INT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------
-- 3. SENSOR SOIL MOISTURE (ESP2)
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS sensor_soil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    soil INT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------
-- 4. ACTUATOR (FAN / LIGHT / PUMP)
--    — BERDASARKAN get_data_new.php
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS actuator (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,   -- HARUS ADA, DIPAKAI OLEH get_data_new.php
    status TINYINT(1) DEFAULT 0,
    auto_enabled TINYINT(1) DEFAULT 0,
    last_auto_trigger TIMESTAMP NULL DEFAULT NULL
);

INSERT INTO actuator (code, status) VALUES
('fan', 0),
('light', 0),
('pump', 0)
ON DUPLICATE KEY UPDATE code = code;

-- ----------------------------------------------
-- 5. SENSOR THRESHOLDS (AUTO MODE)
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS sensor_thresholds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sensor_type VARCHAR(50) NOT NULL UNIQUE,
    min_value FLOAT DEFAULT NULL,
    max_value FLOAT DEFAULT NULL,
    actuator_code VARCHAR(50) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO sensor_thresholds (sensor_type, min_value, max_value, actuator_code) VALUES
('temperature', 20.0, 35.0, 'fan'),
('humidity', 40.0, 80.0, NULL),
('light', 300, 1000, 'light'),
('soil', 30.0, 70.0, 'pump')
ON DUPLICATE KEY UPDATE sensor_type=sensor_type;

-- ----------------------------------------------
-- 6. SYSTEM SETTINGS
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO system_settings (setting_key, setting_value) VALUES
('auto_mode', '0'),
('alert_enabled', '1'),
('refresh_interval', '5000')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- ----------------------------------------------
-- 7. DEVICE STATUS (HEARTBEAT ESP1 / ESP2)
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS device_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_code VARCHAR(50) NOT NULL UNIQUE,
    device_name VARCHAR(100),
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_online TINYINT(1) DEFAULT 1,
    ip_address VARCHAR(45)
);

INSERT INTO device_status (device_code, device_name, is_online) VALUES
('ESP1', 'ESP8266 - Sensor Utama', 0),
('ESP2', 'ESP8266 - Sensor Tanah', 0)
ON DUPLICATE KEY UPDATE device_code=device_code;

-- ----------------------------------------------
-- 8. LOG AKTIVITAS UNTUK DASHBOARD
-- ----------------------------------------------
CREATE TABLE IF NOT EXISTS log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    detail TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------
-- 9. ALERT HISTORY (POWERED by auto mode)
-- ----------------------------------------------
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

-- ----------------------------------------------
-- 10. SENSOR STATISTICS (OPSIONAL)
-- ----------------------------------------------
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

-- ----------------------------------------------
-- 11. INDEX UNTUK PERFORMA
-- ----------------------------------------------
CREATE INDEX idx_suhu_waktu ON sensor_suhu_kelembapan(waktu);
CREATE INDEX idx_ldr_waktu ON sensor_ldr(waktu);
CREATE INDEX idx_soil_waktu ON sensor_soil(waktu);
CREATE INDEX idx_log_created ON log_aktivitas(created_at);
CREATE INDEX idx_alert_created ON alert_history(created_at);

USE smartgreenhouse;

-- pastikan kolom yang diperlukan ada
SHOW COLUMNS FROM actuator LIKE 'manual_override';

ALTER TABLE actuator
  ADD COLUMN manual_override TINYINT(1) DEFAULT 0;

ALTER TABLE actuator
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Jika actuator belum punya code/status, pastikan ter-seed
INSERT INTO actuator (code, status, auto_enabled, manual_override)
VALUES
  ('fan', 0, 0, 0),
  ('light', 0, 0, 0),
  ('pump', 0, 0, 0)
ON DUPLICATE KEY UPDATE code = code;

 SHOW TABLES;
