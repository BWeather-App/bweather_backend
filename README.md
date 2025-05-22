# 🌤️ BWeather - Aplikasi Cuaca Berbasis API Tomorrow.io

**BWeather** adalah aplikasi cuaca ringan berbasis PHP native yang menyajikan prakiraan cuaca real-time, data historis (kemarin), dan ramalan 3 hari ke depan berdasarkan **lokasi pengguna**.

Aplikasi ini dirancang untuk backend dari aplikasi mobile yang dibangun dengan Flutter, dan menggunakan API dari [Tomorrow.io](https://www.tomorrow.io/) untuk keakuratan data cuaca.

---

## 🚀 Fitur Utama

- 🌍 **Realtime Weather** berdasarkan koordinat (GPS) atau nama kota
- 📅 Cuaca: **kemarin**, **hari ini**, dan **3 hari ke depan**
- 🌬️ Kecepatan & arah angin
- ☀️ Informasi astronomi: **matahari terbit/terbenam**, **bulan terbit/terbenam**
- 💧 Kelembaban & tekanan udara
- 🔥 Indeks UV dan suhu terasa
- ☁️ Peluang hujan per jam
- 🔎 Fitur pencarian kota

---

## 🗂 Struktur Folder

    bweather-backend/
    - config/ # Konfigurasi API key & URL
    - controllers/ # Logika bisnis: WeatherController, SearchController
    - routes/ # Routing endpoint API
    - public/ # Entry point index.php

---

## 📦 API External

🔗 Tomorrow.io
Menggunakan 3 endpoint:

    - /realtime untuk cuaca saat ini

    - /history/recent untuk cuaca kemarin

    - /forecast untuk ramalan per jam dan astronomi

---

## ⚙️ Cara Menjalankan (Local)

1. Clone repo ini

   - git clone https://github.com/username/bweather-backend.git

2. Buat file .env dan isi:
   - TOMORROW_BASE_URL=https://api.tomorrow.io/v4/weather
   - TOMORROW_API_KEY=masukkan_api_key_anda

---

## 👨‍💻 Developer

    - Backend: Tim Bweather-Backend
    - Dibuat dengan 💛 menggunakan PHP Native
