#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

const char *ssid = "Apalah";
const char *password = "";
const char *server = "http://10.197.218.68:8000/api";
const char *deviceCode = "ESP2";

#define SOIL_PIN A0
#define RELAY1 D1 // Pompa

unsigned long lastHeartbeat = 0;
const unsigned long heartbeatInterval = 5000; // 5 seconds

void setup()
{
    Serial.begin(115200);
    WiFi.begin(ssid, password);

    pinMode(SOIL_PIN, INPUT);
    pinMode(RELAY1, OUTPUT);

    digitalWrite(RELAY1, LOW);

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
        Serial.println("WiFi terputus, mencoba koneksi ulang...");
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
    int soilValue = analogRead(SOIL_PIN);

    // Convert to percentage (adjust based on your sensor calibration)
    // Typically: wet = low value (200-300), dry = high value (800-1000)
    float soilPercent = map(soilValue, 1024, 0, 0, 100);
    soilPercent = constrain(soilPercent, 0, 100);

    WiFiClient client;
    HTTPClient http;

    String url = String(server) + "/insert_esp2.php";
    http.begin(client, url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "soil=" + String(soilPercent, 1);

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
        // Expected format: {"pump":1}

        // Check for pump status
        int pumpStart = payload.indexOf("\"pump\":");
        if (pumpStart > 0)
        {
            int pumpValue = payload.charAt(pumpStart + 7) - '0';
            digitalWrite(RELAY1, pumpValue == 1 ? HIGH : LOW);
            Serial.println("Pump: " + String(pumpValue == 1 ? "ON" : "OFF"));
        }
    }
    else
    {
        Serial.println("Failed to get actuator status: " + String(httpCode));
    }

    http.end();
}
