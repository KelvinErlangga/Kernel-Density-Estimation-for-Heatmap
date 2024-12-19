# Project Title

Project Based Virtual Intership: Maxy CV Generator Student.

Akun admin :
email : super@admin.com
pw : 123123123

## Prasyarat

Pastikan Anda memiliki hal-hal berikut yang telah diinstal di sistem Anda sebelum memulai:

- **Git**: Untuk mengelola kode.
- **Composer**: Untuk mengelola dependensi PHP.
- **PHP**: Versi minimal 7.4.33.
- **Database**: MySQL.

## Instalasi

Ikuti langkah-langkah berikut untuk menginstal project ini secara lokal:

1. **Clone repositori**:
   ```bash
   git clone https://github.com/Richarddigaa/maxy-cv-generator-student
   ```
   Gantilah `<repository-url>` dengan URL repositori GitHub Anda.

2. **Masuk ke direktori project**:
   ```bash
   cd <project-folder>
   ```
   Gantilah `<project-folder>` dengan nama folder project Anda.

3. **Instal dependensi menggunakan Composer**:
   ```bash
   composer install
   ```

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Migrasi database**:
   Jalankan perintah berikut untuk menghapus tabel lama, membuat tabel baru, dan mengisi data awal:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Jalankan server lokal**:
   Untuk menjalankan server pengembangan lokal, gunakan perintah berikut:
   ```bash
   php artisan serve
   ```

7. **Akses aplikasi**:
   Buka browser Anda dan kunjungi [http://127.0.0.1:8000](http://127.0.0.1:8000) untuk melihat aplikasi Anda.

## Catatan Tambahan

- Pastikan file `.env` sudah disesuaikan dengan konfigurasi database Anda.
- Jika Anda menghadapi kendala, pastikan Anda membaca log error dan menyelesaikan masalah yang disebutkan.


