SIMOARA: Sistem Pemantauan Konsumsi Air Rumah Tangga
SIMOARA (Sistem Pemantauan Konsumsi Air Rumah Tangga) adalah aplikasi web lengkap yang dibangun menggunakan Laravel 10. Aplikasi ini berfungsi sebagai platform pusat untuk mengelola dan memvisualisasikan data konsumsi air yang diterima dari perangkat IoT (sensor aliran dan tekanan air).

Sistem ini dirancang untuk menyajikan data yang kompleks ke dalam dashboard yang informatif dan mudah digunakan, dengan alur data IoT → Web (Aplikasi Ini) → Mobile (Opsional).

Live Demo
Aplikasi ini dapat diakses secara publik di: www.simoara.com

🚀 Fitur Utama
📡 Pemantauan Real-time: Dashboard dinamis untuk memantau konsumsi air, status perangkat, dan metrik penting lainnya.

👤 Manajemen Multi-Role: Hak akses terpisah untuk tiga peran utama: Admin, Teknisi, dan Pengguna (Pelanggan).

📟 Manajemen Perangkat IoT: Teknisi dapat mendaftarkan, mengelola, dan menugaskan (assign) perangkat sensor ke pelanggan.

📊 Pelaporan Dinamis: Admin dan Teknisi dapat mengunduh laporan penggunaan air, keluhan, dan data perangkat dalam format PDF dan Excel, lengkap dengan filter tanggal.

🔔 Sistem Keluhan & Notifikasi: Pelanggan dapat mengirimkan keluhan (termasuk foto), dan teknisi dapat merespons serta mengubah status tiket.

🛡️ Log Aktivitas: Admin dapat memantau seluruh aktivitas penting yang dilakukan oleh teknisi di dalam sistem untuk kebutuhan audit dan keamanan.

🛠️ Teknologi & Paket Utama
Backend: Laravel 10

Database: MySQL / MariaDB (dapat juga menggunakan PostgreSQL)

Autentikasi & Peran: spatie/laravel-permission

Log Aktivitas: spatie/laravel-activitylog

Pembuatan Laporan: barryvdh/laravel-dompdf (untuk PDF) & maatwebsite/excel (untuk Excel)

📦 Instalasi & Persiapan (Backend Laravel)
Bagian ini menjelaskan cara menginstal dan menjalankan backend aplikasi di lingkungan lokal.

1. Persiapan Awal
Sebelum memulai, pastikan perangkat lunak berikut sudah terinstal di sistem Anda:

PHP: Versi 8.1 atau yang lebih baru.

Composer: Manajer paket PHP. Unduh di getcomposer.org.

Server Database: MySQL atau MariaDB.

Git: Untuk mengkloning repositori. Unduh di git-scm.com.

2. Instalasi Backend
Clone repository ini:

Bash

git clone https://github.com/[NAMA_PENGGUNA_ANDA]/[NAMA_REPO_ANDA].git
cd [NAMA_REPO_ANDA]
Instal dependensi Composer:

Bash

composer install
Buat file environment: Salin file .env.example menjadi .env.

Bash

cp .env.example .env
Buat Kunci Aplikasi (App Key):

Bash

php artisan key:generate
Konfigurasi Database: Buka file .env dan sesuaikan pengaturan database Anda:

Cuplikan kode

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simoara
DB_USERNAME=root
DB_PASSWORD=
Jalankan Migrasi & Seeder Database: Perintah ini akan membuat semua tabel database dan mengisinya dengan data awal (termasuk akun admin, teknisi, dan pengguna).

Bash

php artisan migrate --seed
Buat Symbolic Link: Penting untuk membuat storage link agar gambar profil dan foto keluhan dapat diakses.

Bash

php artisan storage:link
3. Menjalankan Aplikasi
Di dalam direktori proyek, jalankan server pengembangan Laravel:

Bash

php artisan serve
Aplikasi backend sekarang berjalan. Anda akan melihat output di konsol:

  INFO  Server running on [http://127.0.0.1:8000].
Buka browser dan kunjungi http://127.0.0.1:8000 untuk melihat halaman landing page atau login.

4. Akun Demo
Setelah menjalankan migrate --seed, Anda dapat login menggunakan akun demo berikut:

Admin:

Email: admin@gmail.com

Password: password

Teknisi:

Email: teknisi@gmail.com

Password: password

Pengguna (Pelanggan):

Email: pengguna@gmail.com

Password: password

🔗 Repositori Terkait
Sistem ini terdiri dari beberapa bagian:

Backend (Aplikasi Ini): [Link-Repo-Backend-Anda]

Aplikasi Mobile (Flutter): [Link-Repo-Mobile-Anda-Jika-Ada]

Firmware IoT (ESP32/Arduino): [Link-Repo-Firmware-Anda-Jika-Ada]

📸 Screenshot Proyek
<div align="center"> <p><strong>[Landing Page]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Landing Page" width="700"/> </div>


<div align="center"> <p><strong>[Halaman Login]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Login" width="700"/> </div>


<div align="center"> <p><strong>[Dashboard Admin - Monitoring Konsumsi]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Dashboard Admin" width="700"/> </div>


<div align="center"> <p><strong>[Dashboard Admin - Log Aktivitas Teknisi]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Admin Log" width="700"/> </div>


<div align="center"> <p><strong>[Manajemen Perangkat (Admin/Teknisi)]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Manajemen Perangkat" width="700"/> </div>


<div align="center"> <p><strong>[Dashboard Pengguna]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Dashboard Pengguna" width="700"/> </div>


<div align="center"> <p><strong>[Halaman Monitoring Detail (Pengguna)]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Monitoring Pengguna" width="700"/> </div>


<div align="center"> <p><strong>[Halaman Keluhan]</strong></p> <img src="[GANTI_DENGAN_URL_GAMBAR_ANDA]" alt="Halaman Keluhan" width="700"/> </div>
