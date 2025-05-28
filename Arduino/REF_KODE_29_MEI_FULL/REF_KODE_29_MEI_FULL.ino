#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <max6675.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
#include <LiquidCrystal_I2C.h>
#include <Adafruit_INA219.h>

// Konfigurasi WiFi
const char* ssid = "somey";
const char* password = "skibidii";

// Endpoint Azure
const char* serverUrl = "https://pengeringkopiiot.azurerobotic.my.id/api/getdatasensor";

// Inisialisasi Sensor
Adafruit_BME280 bme;
Adafruit_INA219 ina219;
LiquidCrystal_I2C lcd(0x27, 20, 4);

// Pin
#define relayHeaterPin 2
#define relayFanPin 4
#define relayMotorStopPin 5
#define motorIn1 27
#define motorIn2 26
#define motorPWMChannel 14
#define motorPWMPin 14

// Thermocouple
int thermoCLK = 5;
int thermoCS = 23;
int thermoDO = 19;
MAX6675 thermocouple(thermoCLK, thermoCS, thermoDO);

// RPM Sensor
#define SENSOR_PIN 25
volatile int pulseCount = 0;
unsigned long lastRPMCheck = 0;
int rpm = 0;

// LCD
unsigned long lastLCDUpdate = 0;
int lcdPage = 0;
int speed;

void IRAM_ATTR handleInterrupt() {
  pulseCount++;
}

void setup() {
  Serial.begin(115200);
  lcd.init();
  lcd.backlight();

  // Inisialisasi BME280
  if (!bme.begin(0x76)) {
    lcd.print("BME280 Error!");
    while (1);
  }

  // Inisialisasi INA219
  if (!ina219.begin()) {
    lcd.print("INA219 Error!");
    while (1);
  }

  // Setup Pin
  pinMode(relayHeaterPin, OUTPUT);
  pinMode(relayFanPin, OUTPUT);
  pinMode(relayMotorStopPin, OUTPUT);
  pinMode(motorIn1, OUTPUT);
  pinMode(motorIn2, OUTPUT);
  ledcSetup(motorPWMChannel, 1000, 8);
  ledcAttachPin(motorPWMPin, motorPWMChannel);

  // Setup Interrupt RPM
  pinMode(SENSOR_PIN, INPUT_PULLUP);
  attachInterrupt(digitalPinToInterrupt(SENSOR_PIN), handleInterrupt, FALLING);

  // Koneksi WiFi
  WiFi.begin(ssid, password);
  lcd.clear();
  lcd.print("Connecting to WiFi");
  Serial.println("Connecting to WiFi...");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    lcd.print(".");
  }
  
  lcd.clear();
  lcd.print("WiFi Connected!");
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  lastRPMCheck = millis();
  lastLCDUpdate = millis();
}

void loop() {
  // Baca semua sensor
  float suhu = thermocouple.readCelsius();
  float suhuBME = bme.readTemperature();
  float hum = bme.readHumidity();
  float current_mA = ina219.getCurrent_mA();
  float busvoltage = ina219.getBusVoltage_V();
  float shuntvoltage = ina219.getShuntVoltage_mV();
  float power_mW = ina219.getPower_mW();
  float loadvoltage = busvoltage + (shuntvoltage / 1000.0);

  // Hitung RPM
  if (millis() - lastRPMCheck >= 1000) {
    detachInterrupt(digitalPinToInterrupt(SENSOR_PIN));
    rpm = pulseCount * 60;
    pulseCount = 0;
    lastRPMCheck = millis();
    attachInterrupt(digitalPinToInterrupt(SENSOR_PIN), handleInterrupt, FALLING);
  }

  // Update LCD
  if (millis() - lastLCDUpdate >= 3000) {
    updateLCD(suhu, suhuBME, hum, rpm, current_mA, loadvoltage);
    lastLCDUpdate = millis();
  }

  // Kontrol Relay berdasarkan suhu
  String relayFan = suhu > 60 ? "ON" : "OFF";
  String relayHeater = suhu < 60 ? "ON" : "OFF";
  
  digitalWrite(relayFanPin, relayFan == "ON" ? LOW : HIGH);
  digitalWrite(relayHeaterPin, relayHeater == "ON" ? LOW : HIGH);

  // Kontrol Motor
  speed = fuzzyMotorSpeed(suhu);
  if (suhu < 60) {
    digitalWrite(relayMotorStopPin, LOW);
    digitalWrite(motorIn1, HIGH);
    digitalWrite(motorIn2, LOW);
    ledcWrite(motorPWMChannel, speed);
    Serial.println("suhu panah heater nyala");
  } else {
    stopMotor();
    digitalWrite(relayMotorStopPin, HIGH);
  }

  // Kirim data ke server setiap 10 detik
  static unsigned long lastSendTime = 0;
  if (millis() - lastSendTime >= 1000) {
    sendDataToServer(suhu, rpm, current_mA, relayFan, relayHeater);
    lastSendTime = millis();
  }

  // Debug Serial
  printSensorData(suhu, suhuBME, hum, current_mA, busvoltage, shuntvoltage, loadvoltage, power_mW, rpm);
  
  delay(100);
}

void updateLCD(float suhu, float suhuBME, float hum, int rpm, float current_mA, float loadvoltage) {
  lcd.clear();
  switch (lcdPage) {
    case 0:
      lcd.setCursor(0, 0);
      lcd.print("Suhu MAX6675: ");
      lcd.print(suhu, 1);
      lcd.print("C");
      lcd.setCursor(0, 1);
      lcd.print("Suhu BME: ");
      lcd.print(suhuBME, 1);
      lcd.print("C");
      lcd.setCursor(0, 2);
      lcd.print("Humidity: ");
      lcd.print(hum, 1);
      lcd.print("%");
      break;
    case 1:
      lcd.setCursor(0, 0);
      lcd.print("RPM: ");
      lcd.print(rpm);
      lcd.setCursor(0, 1);
      lcd.print("PWM: ");
      lcd.print(speed);
      lcd.print("");
      lcd.setCursor(0, 2);
      lcd.print("Current: ");
      lcd.print(current_mA, 1);
      lcd.print("mA");
      break;
    case 2:
      lcd.setCursor(0, 0);
      lcd.print("Heater: ");
      lcd.print(digitalRead(relayHeaterPin) == LOW ? "ON " : "OFF");
      lcd.print(" Fan: ");
      lcd.print(digitalRead(relayFanPin) == LOW ? "ON" : "OFF");
      lcd.setCursor(0, 1);
      lcd.print("Motor: ");
      lcd.print(digitalRead(relayMotorStopPin) == LOW ? "ON" : "OFF");
      lcd.setCursor(0, 2);
      lcd.print("Volt: ");
      lcd.print(loadvoltage, 1);
      lcd.print("V");
      break;
  }
  lcdPage = (lcdPage + 1) % 3;
}

void sendDataToServer(float suhu, int rpm, float arus, String relayFan, String relayHeater) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // Buat payload JSON
    DynamicJsonDocument doc(200);
    doc["suhu"] = suhu;
    doc["rpm"] = rpm;
    doc["arus"] = arus;
    doc["relayFan"] = relayFan;
    doc["relayHeater"] = relayHeater;
    
    String payload;
    serializeJson(doc, payload);
    
    // Mulai koneksi HTTP
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    
    // Kirim request POST
    int httpResponseCode = http.POST(payload);
    
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      Serial.print("Response: ");
      Serial.println(response);
      
      // Tampilkan di LCD baris ke-4
      lcd.setCursor(0, 3);
      lcd.print("Send: ");
      lcd.print(httpResponseCode == 200 ? "OK" : "Error");
    } else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
      Serial.println("Error sending POST request");
      
      lcd.setCursor(0, 3);
      lcd.print("Send: Failed");
    }
    
    // Tutup koneksi
    http.end();
  } else {
    Serial.println("WiFi Disconnected");
    lcd.setCursor(0, 3);
    lcd.print("WiFi Disconnected");
    
    // Coba reconnect WiFi
    WiFi.begin(ssid, password);
  }
}

void printSensorData(float suhu, float suhuBME, float hum, float current_mA, float busvoltage, 
                    float shuntvoltage, float loadvoltage, float power_mW, int rpm) {
  Serial.println("=======================================");
  Serial.print("Suhu MAX6675 : "); Serial.print(suhu); Serial.println(" °C");
  Serial.print("Suhu BME     : "); Serial.print(suhuBME); Serial.println(" °C");
  Serial.print("Humidity     : "); Serial.print(hum); Serial.println(" %");
  Serial.print("Current      : "); Serial.print(current_mA); Serial.println(" mA");
  Serial.print("Bus Voltage  : "); Serial.print(busvoltage); Serial.println(" V");
  Serial.print("Shunt Volt   : "); Serial.print(shuntvoltage); Serial.println(" mV");
  Serial.print("Load Voltage : "); Serial.print(loadvoltage); Serial.println(" V");
  Serial.print("Power        : "); Serial.print(power_mW); Serial.println(" mW");
  Serial.print("RPM          : "); Serial.println(rpm);
  Serial.println("=======================================");
}

void stopMotor() {
  ledcWrite(motorPWMChannel, 0);
  digitalWrite(motorIn1, LOW);
  digitalWrite(motorIn2, LOW);
}

// Fungsi Keanggotaan Fuzzy yang Disempurnakan
float dingin(float x) {
  if (x <= 30) return 1.0;           // ≤30°C = 100% dingin
  else if (x > 30 && x <= 45) return (45 - x)/15.0;  // Turun linear 30-45°C
  else return 0.0;                   // >45°C = tidak dingin
}

float sedang(float x) {
  if (x <= 30 || x >= 70) return 0.0;  // Di luar range = tidak sedang
  else if (x > 30 && x <= 50) return (x - 30)/20.0;  // Naik 30-50°C
  else if (x > 50 && x < 70) return (70 - x)/20.0;   // Turun 50-70°C
  else return 0.0;
}

float panas(float x) {
  if (x <= 55) return 0.0;           // ≤55°C = tidak panas
  else if (x > 55 && x <= 70) return (x - 55)/15.0;  // Naik 55-70°C
  else return 1.0;                   // ≥70°C = 100% panas
}

int fuzzyMotorSpeed(float suhu) {
  float u_dingin = dingin(suhu);
  float u_sedang = sedang(suhu);
  float u_panas = panas(suhu);

  // Aturan fuzzy:
  // 1. Jika DINGIN -> motor LAMBAT (PWM 50)
  // 2. Jika SEDANG -> motor SEDANG (PWM 128)
  // 3. Jika PANAS -> motor CEPAT (PWM 255)
  
  float pwm = (u_dingin * 50 + u_sedang * 128 + u_panas * 255) / (u_dingin + u_sedang + u_panas);
  
  // Pastikan nilai PWM dalam range 0-255
  return constrain((int)pwm, 0, 255);
}