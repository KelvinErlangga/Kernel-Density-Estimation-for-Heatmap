# CV Generator - Student Version

**Sistem Generator CV dengan Analisis Heatmap Sebaran Lowongan Kerja**

Project skripsi yang mengintegrasikan pembuatan CV dengan visualisasi heatmap untuk menganalisis persebaran lokasi lowongan pekerjaan berdasarkan skill dan preferensi pengguna.

---

## 📋 Ringkasan Project

CV Generator adalah platform web berbasis Laravel yang dirancang untuk membantu mahasiswa dan fresh graduate dalam:
- Membuat CV profesional secara otomatis
- Menganalisis sebaran lowongan kerja dengan visualisasi heatmap
- Memberikan insight tentang peluang kerja berdasarkan lokasi dan skill
- Mempermudah proses aplikasi kerja dengan pendekatan data-driven

---

## 🌟 Fitur Utama

1. **Manajemen CV Digital**
   - Pembuatan CV dengan template profesional
   - Input data pribadi, pendidikan, pengalaman, dan skill
   - Export CV ke format PDF
   - Multiple CV untuk berbagai keperluan

2. **Heatmap Analisis Lowongan**
   - Visualisasi sebaran lowongan kerja berdasarkan lokasi geografis
   - Filter berdasarkan skill yang dimiliki
   - Intensitas heatmap menunjukkan konsentrasi lowongan
   - Interaktif dengan peta Leaflet

3. **Portal Lowongan Kerja**
   - Manajemen data lowongan oleh perusahaan
   - Pencarian dan filter lowongan
   - Detail informasi lowongan dengan lokasi

4. **Sistem Multi-User**
   - Role-based access (Admin, Company, Student)
   - Dashboard personal untuk setiap role
   - Manajemen profil dan preferensi

5. **Analisis & Reporting**
   - Statistik pelamar per lokasi
   - Trend lowongan berdasarkan skill
   - Export data untuk analisis lebih lanjut

---

## 🛠 Tech Stack

### Backend
- **Framework**: Laravel 8.x
- **Language**: PHP 7.4+
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **PDF Generation**: Laravel DomPDF & Snappy
- **Screenshot**: Spatie Browsershot

### Frontend
- **CSS Framework**: Tailwind CSS 3.x
- **JavaScript**: Vanilla JS dengan Alpine.js 3.x
- **Build Tool**: Laravel Mix
- **Maps**: Leaflet.js 1.9.4
- **Heatmap**: Leaflet.heat 0.2.0
- **Charts**: Custom visualization

### Libraries & Dependencies
- **Kernel Density Estimation**: fast-kde 0.2.2
- **Translation**: Google Translate API
- **Social Login**: Laravel Socialite
- **Permissions**: Spatie Laravel Permission
- **HTTP Client**: Guzzle

---

## 🚀 Instalasi

### Prasyarat
- PHP 7.4+ atau 8.0+
- Composer 2.x
- Node.js 16.x+
- MySQL 5.7+ atau 8.0+
- Git

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/Richarddigaa/maxy-cv-generator-student
   cd maxy-cv-generator-student
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   - Edit file `.env` untuk konfigurasi database
   ```env
   DB_DATABASE=maxy_cv_generator
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Database Migration**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Compile Assets**
   ```bash
   npm run dev
   # atau untuk production
   npm run prod
   ```

8. **Start Server**
   ```bash
   php artisan serve
   ```

9. **Akses Aplikasi**
   - URL: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 👥 Akun Demo

### Admin
- **Email**: super@admin.com
- **Password**: 123123123

### Student (Default Seeder)
- **Email**: student@example.com
- **Password**: password

### Company (Default Seeder)
- **Email**: company@example.com
- **Password**: password

---

## 📁 Struktur Project

```
maxy-cv-generator-student/
├── app/
│   ├── Http/Controllers/     # Logic controller
│   ├── Models/              # Eloquent models
│   └── Helpers/             # Utility functions
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/            # Initial data
├── resources/
│   ├── views/              # Blade templates
│   └── js/                 # Frontend assets
├── public/                 # Static assets
├── routes/                 # API & Web routes
└── storage/                # File storage
```

---

## 🔧 Konfigurasi Tambahan

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maxy_cv_generator
DB_USERNAME=root
DB_PASSWORD=

# Application
APP_NAME="CV Generator"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Mail (opsional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Permission Setup
```bash
# Pastikan folder storage writable
chmod -R 775 storage bootstrap/cache
```

---

## 🐛 Troubleshooting

### Common Issues
1. **Composer Install Error**
   - Jalankan `composer install --no-dev` untuk production
   - Clear cache: `composer clear-cache`

2. **NPM Build Error**
   - Hapus node_modules: `rm -rf node_modules`
   - Install ulang: `npm install`

3. **Database Migration Error**
   - Pastikan database sudah dibuat
   - Check koneksi database di `.env`

4. **Storage Permission**
   ```bash
   php artisan storage:link
   chmod -R 775 storage
   ```

---

## 📄 Lisensi

Project ini dilisensikan under MIT License - lihat file [LICENSE](LICENSE) untuk detail.

---

## 🤝 Kontribusi

Kontribusi sangat welcome! Silakan:
1. Fork project ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

---

## 📞 Kontak

- **Developer**: Richard Diga
- **Email**: richard@example.com
- **Project**: Skripsi S1 Teknik Informatika

---

## 🙏 Acknowledgments

- Laravel Framework & Community
- Leaflet.js untuk peta interaktif
- Tailwind CSS untuk styling modern
- Semua pihak yang telah membantu pengembangan project ini

