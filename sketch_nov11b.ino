#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

const char* ssid = "Apalah";
const char* password = "";
const char* server = "http://10.197.218.68:8000/api";

#define SOIL_PIN A0
#define RELAY1 D1

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  pinMode(SOIL_PIN, INPUT);
  pinMode(RELAY1, OUTPUT);

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
    int soilValue = analogRead(SOIL_PIN);

    // --- Kirim data soil ke server ---
    WiFiClient client;
    HTTPClient http;
    String url = String(server) + "/insert_esp2.php";

    http.begin(client, url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "soil=" + String(soilValue);
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

    String dataRelay = "code=relay1&status=0"; 
    int httpCode = http2.POST(dataRelay);

    if (httpCode > 0) {
      String payload = http2.getString();
      Serial.println("Status relay: " + payload);

    
      if (payload.indexOf("\"success\":true") > 0 && payload.indexOf("\"status\":1") > 0) {
        digitalWrite(RELAY1, HIGH);
      } else {
        digitalWrite(RELAY1, LOW);
      }
    } else {
      Serial.print("Gagal ambil status relay, code: ");
      Serial.println(httpCode);
    }
    http2.end();

  } else {
    Serial.println("WiFi terputus, mencoba koneksi ulang...");
    WiFi.reconnect();
  }

  delay(5000);
}
