#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>

#define DHTPIN D4
#define DHTTYPE DHT22
#define LDR_PIN A0

#define RELAY1 D1
#define RELAY2 D2

const char* ssid = "Apalah";
const char* password = "";

const char* server = "http://10.197.218.68:8000/api";

DHT dht(DHTPIN, DHTTYPE);

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  pinMode(LDR_PIN, INPUT);
  pinMode(RELAY1, OUTPUT);
  pinMode(RELAY2, OUTPUT);
  dht.begin();

  Serial.print("Menghubungkan ke WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nTerhubung ke WiFi!");
  Serial.print("IP ESP: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    // --- Baca sensor ---
    float humidity = dht.readHumidity();
    float temperature = dht.readTemperature();
    int ldrValue = analogRead(LDR_PIN);

    if (isnan(humidity) || isnan(temperature)) {
      Serial.println("Gagal membaca DHT22!");
      delay(2000);
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
    Serial.print("Kirim data ke server: ");
    Serial.println(httpResponseCode);
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Respon server: " + response);
    } else {
      Serial.println("Gagal kirim data: " + String(httpResponseCode));
    }
    http.end();

    HTTPClient http2;
    String urlActuator = String(server) + "/update_actuator.php";
    http2.begin(client, urlActuator);
    http2.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String actuatorData = "code=ESP1&status=1"; 
    int actResponse = http2.POST(actuatorData);

    if (actResponse > 0) {
      String payload = http2.getString();
      Serial.println("Status aktuator: " + payload);

      if (payload.indexOf("\"success\":true") > 0) {
        digitalWrite(RELAY1, HIGH); 
        digitalWrite(RELAY2, LOW);  
      } else {
        digitalWrite(RELAY1, LOW);
        digitalWrite(RELAY2, LOW);
      }
    } else {
      Serial.println("Gagal update aktuator: " + String(actResponse));
    }
    http2.end();
  } else {
    Serial.println("WiFi terputus, mencoba reconnect...");
    WiFi.reconnect();
  }

  delay(5000); 
}
