# SIMOARA: Sistem Pemantauan Konsumsi Air Rumah Tangga

**SIMOARA** (Sistem Pemantauan Konsumsi Air Rumah Tangga) adalah aplikasi web berbasis **Laravel 10** yang berfungsi untuk memantau, mengelola, dan menganalisis data konsumsi air rumah tangga yang dikirim dari perangkat **IoT** (sensor aliran dan tekanan air).  
Sistem ini menyajikan data kompleks dalam bentuk dashboard yang informatif, dengan alur komunikasi **IoT → Web (Aplikasi Ini) → Mobile (Opsional)**.

## 🚀 Fitur Utama

- 📡 **Pemantauan Real-time**: Dashboard dinamis untuk melihat konsumsi air, status perangkat, dan metrik penting lainnya.
- 👤 **Manajemen Multi-Role**: Akses terpisah untuk **Admin**, **Teknisi**, dan **Pengguna (Pelanggan)**.
- 📟 **Manajemen Perangkat IoT**: Teknisi dapat menambahkan, mengedit, dan menugaskan perangkat sensor ke pelanggan.
- 📊 **Pelaporan Dinamis**: Unduh laporan penggunaan air, keluhan, dan data perangkat dalam format **PDF** dan **Excel**.
- 🔔 **Sistem Keluhan & Notifikasi**: Pengguna dapat mengirim keluhan (dengan foto), teknisi dapat merespons dan memperbarui status.
- 🛡️ **Log Aktivitas**: Admin dapat melacak aktivitas teknisi dan perubahan penting di sistem.

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 10
- **Database**: MySQL / MariaDB
- **Autentikasi & Role**: spatie/laravel-permission
- **Log Aktivitas**: spatie/laravel-activitylog
- **Export Laporan**: barryvdh/laravel-dompdf & maatwebsite/excel

## 1. Persiapan Awal

Sebelum memulai, pastikan perangkat lunak berikut sudah terinstal di sistem:

* *PHP*: Versi 8.1 atau lebih baru.
* *Composer*: Unduh di [getcomposer.org](https://getcomposer.org/).
* *Database Server*: MySQL atau MariaDB.
* *Git*: Untuk mengkloning repositori. Unduh di [git-scm.com](https://git-scm.com/).

## 📦 Instalasi

1. **Clone repository ini:**

   ```bash
   git clone https://github.com/username/simoara.git
   cd simoara

2. **Instal dependensi Composer:**

   ```bash
   composer install

3. **Buat file environment (.env):**

   ```bash
   cp .env.example .env

4. **Buat Kunci Aplikasi (App Key):**
   ```bash
   php artisan key:generate

5. **Konfigurasi Database:**
Buka file .env dan sesuaikan pengaturan berikut:

    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=simoara
    DB_USERNAME=root
    DB_PASSWORD=

6. **Jalankan Migrasi & Seeder Database:**
   ```bash
    php artisan migrate --seed

7. **Buat Symbolic Link:**
    ```bash
    php artisan storage:link

7. **Menjalankan Aplikasi**
Setelah semua dependensi terinstal, jalankan server pengembangan Laravel:
    ```bash
    php artisan serve


*Akan muncul output seperti ini di terminal:*
    ```bash
    INFO  Server running on [http://127.0.0.1:8000].


*Buka browser dan akses http://127.0.0.1:8000.*
*Aplikasi SIMOARA sekarang berjalan di browser Anda.*

9. **Akun Demo**
Setelah menjalankan
    ```bash
    php artisan migrate --seed

**Anda dapat login menggunakan akun demo berikut:**

        Admin    
        Email: admin@admin.com
        Password: Admin#123
        
        Teknisi
        Email: teknisi.udin@dummy.com
        Password: Teknisi#123

        Pengguna        
        Email: lucky@dummy.com
        Password: Lucky#123

## 📸 Screenshot Proyek

<div align="center">
  <p><strong>[Landing Page]</strong></p>
  <img src="https://github.com/user-attachments/assets/d604226a-1c8c-4d70-afbb-4ea021a4813d" alt="Landing Page" width="700"/>
</div>
<br>

<div align="center">
  <p><strong>[Halaman Login]</strong></p>
  <img src="https://github.com/user-attachments/assets/49397bc0-a038-4184-90ca-813d2b6cb219" alt="Login" width="700"/>
</div>
<br>

<div align="center">
  <p><strong>[Dashboard Admin]</strong></p>
  <img src="https://github.com/user-attachments/assets/e7c52813-c5e9-46a8-b885-27e84dcd539e" alt="Dashboard Admin" width="700"/>
</div>
<br>

<div align="center">
  <p><strong>[Dashboard Teknisi]</strong></p>
  <img src="https://github.com/user-attachments/assets/f0953f18-f88a-4daa-ab09-6c8f197d789f" alt="Dashboard Teknisi" width="700"/>
</div>
<br>

<div align="center">
  <p><strong>[Dashboard Pengguna]</strong></p>
  <img src="https://github.com/user-attachments/assets/e165ce86-3714-49ca-be2e-591a996a6c91" alt="Dashboard Pengguna" width="700"/>
</div>
