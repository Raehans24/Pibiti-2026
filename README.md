# 🧠 Smart Notes AI

<div align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  <br><br>
  <b>Belajar lebih cerdas dengan AI. Buat catatan, rangkum otomatis, dan uji pemahamanmu.</b>
</div>

---

## 🌟 Tentang Aplikasi

**Smart Notes AI** adalah platform pembelajaran interaktif berbasis kecerdasan buatan yang dirancang untuk mempermudah proses belajar Anda. Dibangun menggunakan framework **Laravel**, aplikasi ini memungkinkan Anda untuk mengelola catatan, meng-upload dokumen, menghasilkan ringkasan cerdas secara otomatis, dan bahkan membuat kuis interaktif berdasarkan materi catatan Anda.

Aplikasi ini menggunakan desain UI modern, bersih, dan responsif dengan integrasi mode gelap (*dark mode*) penuh.

---

## ✨ Fitur Utama

*   📝 **Manajemen Catatan:** Buat, edit, dan kelola catatan teks biasa dengan editor yang mulus.
*   📤 **Upload Dokumen:** Mendukung unggahan file `.txt`, `.md`, dan `.pdf`.
*   🤖 **Ringkasan Otomatis (AI):** Ringkas catatan panjang atau dokumen menjadi poin-poin penting dalam hitungan detik.
*   🧠 **Generator Kuis (AI):** Uji pemahaman Anda! Aplikasi akan membaca catatan Anda dan membuat kuis pilihan ganda secara dinamis.
*   🌍 **World Prediction:** Modul prediksi dan informasi dunia yang terintegrasi (Cuaca, Angin, Komoditas, Acara).
*   🔐 **Otentikasi:** Sistem login & registrasi yang aman, termasuk dukungan masuk melalui **Google OAuth**.
*   🎨 **Desain Modern:** Antarmuka responsif yang indah dengan transisi halus dan dukungan Mode Gelap penuh.

---

## 🛠️ Teknologi yang Digunakan

*   **Backend:** [Laravel 11+](https://laravel.com/) (PHP)
*   **Database:** MySQL / SQLite
*   **Frontend:** Blade Templates, HTML5, Vanilla JavaScript
*   **Styling:** [Tailwind CSS v4](https://tailwindcss.com/) (dengan kustom font Inter)
*   **AI Integration:** Laravel AI / OpenAI (atau provider LLM lainnya yang dikonfigurasi)

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi ini secara lokal di mesin Anda.

### 1. Kebutuhan Sistem

*   PHP >= 8.2
*   Composer
*   Node.js & NPM
*   Database (MySQL/MariaDB/SQLite)

### 2. Kloning Repositori

```bash
git clone https://github.com/yourusername/smart-notes-ai.git
cd smart-notes-ai
```

### 3. Instalasi Dependensi

Instal dependensi PHP:
```bash
composer install
```

Instal dependensi Node.js:
```bash
npm install
```

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Buka file `.env` dan atur koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_notes
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan Anda juga menambahkan kredensial API AI Anda (misalnya OpenAI API Key) dan kredensial Google OAuth di file `.env` jika diperlukan.

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Migrasi Database

Jalankan migrasi untuk membuat tabel database:
```bash
php artisan migrate
```

### 7. Compile Aset Frontend

Compile CSS dan JavaScript menggunakan Vite:
```bash
npm run build
# atau untuk development: npm run dev
```

### 8. Jalankan Server

Jalankan server pengembangan lokal Laravel:
```bash
php artisan serve
```

Aplikasi sekarang dapat diakses di: `http://localhost:8000`

---

## 📖 Cara Penggunaan

1.  **Daftar/Masuk:** Buat akun baru atau masuk menggunakan akun Google Anda.
2.  **Dashboard:** Anda akan disambut di dashboard yang menampilkan statistik catatan dan kuis Anda.
3.  **Catatan Baru:** Masuk ke menu "Catatan" untuk menulis catatan baru atau meng-upload file (`.pdf`, `.md`, `.txt`).
4.  **Ringkasan AI:** Buka detail catatan Anda dan klik "Ringkas AI" untuk mendapatkan intisari otomatis dari teks Anda.
5.  **Mulai Kuis:** Klik "Buat Quiz AI" pada catatan Anda. Sistem akan membuat soal-soal. Setelah jadi, klik "Mulai Quiz" untuk menguji pemahaman Anda.

---

## 🤝 Berkontribusi

Kontribusi selalu diterima! Jika Anda menemukan bug atau memiliki ide untuk fitur baru:

1.  Fork repositori ini.
2.  Buat branch fitur Anda (`git checkout -b fitur/FiturKeren`).
3.  Commit perubahan Anda (`git commit -m 'Menambahkan beberapa FiturKeren'`).
4.  Push ke branch (`git push origin fitur/FiturKeren`).
5.  Buka *Pull Request*.

---

## 📄 Lisensi

Proyek ini berlisensi di bawah [MIT License](https://opensource.org/licenses/MIT).
