#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>

#define DHTPIN D4
#define DHTTYPE DHT22
#define LDR_PIN A0

#define RELAY1 D1 // Kipas
#define RELAY2 D2 // Lampu

const char *ssid = "Apalah";
const char *password = "";
const char *server = "http://10.197.218.68:8000/api";
const char *deviceCode = "ESP1";

DHT dht(DHTPIN, DHTTYPE);

unsigned long lastHeartbeat = 0;
const unsigned long heartbeatInterval = 5000; // 5 seconds

void setup()
{
    Serial.begin(115200);
    WiFi.begin(ssid, password);

    pinMode(LDR_PIN, INPUT);
    pinMode(RELAY1, OUTPUT);
    pinMode(RELAY2, OUTPUT);

    digitalWrite(RELAY1, LOW);
    digitalWrite(RELAY2, LOW);

    dht.begin();

    Serial.print("Menghubungkan ke WiFi");
    while (WiFi.status() != WL_CONNECTED)
    {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nTerhubung ke WiFi!");
    Serial.print("IP ESP: ");
    Serial.println(WiFi.localIP());

    // Send initial heartbeat
    sendHeartbeat();
}

void loop()
{
    if (WiFi.status() == WL_CONNECTED)
    {
        unsigned long currentMillis = millis();

        // Send heartbeat every interval
        if (currentMillis - lastHeartbeat >= heartbeatInterval)
        {
            lastHeartbeat = currentMillis;
            sendHeartbeat();
            sendSensorData();
            checkActuatorStatus();
        }
    }
    else
    {
        Serial.println("WiFi terputus, mencoba reconnect...");
        WiFi.reconnect();
        delay(5000);
    }
}

void sendHeartbeat()
{
    WiFiClient client;
    HTTPClient http;

    String url = String(server) + "/heartbeat.php";
    http.begin(client, url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "device_code=" + String(deviceCode) +
                      "&ip=" + WiFi.localIP().toString();

    int httpCode = http.POST(postData);

    if (httpCode > 0)
    {
        Serial.println("Heartbeat sent: " + String(httpCode));
    }
    else
    {
        Serial.println("Heartbeat failed: " + String(httpCode));
    }

    http.end();
}

void sendSensorData()
{
    float humidity = dht.readHumidity();
    float temperature = dht.readTemperature();
    int ldrValue = analogRead(LDR_PIN);

    if (isnan(humidity) || isnan(temperature))
    {
        Serial.println("Gagal membaca DHT22!");
        return;
    }

    WiFiClient client;
    HTTPClient http;

    String url = String(server) + "/insert_esp1.php";
    http.begin(client, url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "suhu=" + String(temperature, 2) +
                      "&kelembapan=" + String(humidity, 2) +
                      "&ldr=" + String(ldrValue);

    int httpResponseCode = http.POST(postData);

    Serial.print("Sensor data sent: ");
    Serial.println(httpResponseCode);

    if (httpResponseCode > 0)
    {
        String response = http.getString();
        Serial.println("Response: " + response);
    }

    http.end();
}

void checkActuatorStatus()
{
    WiFiClient client;
    HTTPClient http;

    String url = String(server) + "/get_actuator_status.php?device=" + String(deviceCode);
    http.begin(client, url);

    int httpCode = http.GET();

    if (httpCode > 0)
    {
        String payload = http.getString();
        Serial.println("Actuator status: " + payload);

        // Parse JSON response (simple parsing)
        // Expected format: {"fan":1,"light":0}

        // Check for fan status
        int fanStart = payload.indexOf("\"fan\":");
        if (fanStart > 0)
        {
            int fanValue = payload.charAt(fanStart + 6) - '0';
            digitalWrite(RELAY1, fanValue == 1 ? HIGH : LOW);
            Serial.println("Fan: " + String(fanValue == 1 ? "ON" : "OFF"));
        }

        // Check for light status
        int lightStart = payload.indexOf("\"light\":");
        if (lightStart > 0)
        {
            int lightValue = payload.charAt(lightStart + 8) - '0';
            digitalWrite(RELAY2, lightValue == 1 ? HIGH : LOW);
            Serial.println("Light: " + String(lightValue == 1 ? "ON" : "OFF"));
        }
    }
    else
    {
        Serial.println("Failed to get actuator status: " + String(httpCode));
    }

    http.end();
}
