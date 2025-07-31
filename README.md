# Portal TPB - Sistem Informasi Akademik

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Tentang Portal TPB

Portal TPB adalah sistem informasi akademik yang dibangun menggunakan framework Laravel untuk mengelola data akademik mahasiswa, dosen, mata kuliah, dan nilai. Sistem ini dirancang untuk mendukung proses pembelajaran dan administrasi akademik.

## Fitur Utama

- **Manajemen User**: Admin, Dosen, Mahasiswa, dan Pimpinan
- **Manajemen Data Akademik**: Mata kuliah, kelas, dosen pengampu
- **Sistem Penilaian**: Input dan pengelolaan nilai mahasiswa
- **Dashboard**: Dashboard khusus untuk setiap role user
- **Laporan**: Export data nilai dan laporan akademik

## Dokumentasi Proyek

### Dokumentasi Tugas

- [001 - Update Seeder Dosen dan Mata Kuliah](./docs/001_update_dosen_mata_kuliah_seeder.md) - Update seeder berdasarkan data Excel

### Template Dokumentasi

- [000 - Template Dokumentasi](./docs/000_TEMPLATE.md) - Template untuk dokumentasi tugas baru

## Instalasi dan Setup

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js & NPM

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd projectTPB-1
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database**
   - Edit file `.env` dan sesuaikan konfigurasi database
   - Jalankan migration dan seeder:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build Assets**
   ```bash
   npm run build
   ```

6. **Jalankan Server**
   ```bash
   php artisan serve
   ```

## Struktur Database

### Tabel Utama

- `users` - Data pengguna sistem
- `mahasiswa` - Data mahasiswa
- `dosen` - Data dosen
- `mata_kuliah` - Data mata kuliah
- `kelas` - Data kelas
- `nilai` - Data nilai mahasiswa
- `tahun_ajaran` - Data tahun ajaran

### Relasi

- Mahasiswa memiliki banyak nilai
- Dosen mengampu banyak kelas
- Mata kuliah dapat diambil di berbagai kelas
- Nilai terkait dengan mahasiswa, dosen, dan mata kuliah

## Role dan Akses

### Admin
- Manajemen user dan data master
- Akses penuh ke semua fitur

### Dosen
- Input dan edit nilai mahasiswa
- Lihat data kelas yang diampu
- Dashboard khusus dosen

### Mahasiswa
- Lihat nilai dan transkrip
- Dashboard mahasiswa

### Pimpinan
- Lihat laporan dan statistik
- Dashboard pimpinan

## API Endpoints

### Authentication
- `POST /login` - Login user
- `POST /logout` - Logout user

### Mahasiswa
- `GET /mahasiswa/dashboard` - Dashboard mahasiswa
- `GET /mahasiswa/transkrip` - Transkrip nilai

### Dosen
- `GET /dosen/dashboard` - Dashboard dosen
- `GET /dosen/nilai` - Data nilai
- `POST /dosen/nilai` - Input nilai

### Admin
- `GET /admin/dashboard` - Dashboard admin
- `GET /admin/mahasiswa` - Data mahasiswa
- `GET /admin/dosen` - Data dosen
- `GET /admin/mata-kuliah` - Data mata kuliah

## Testing

```bash
# Menjalankan semua test
php artisan test

# Menjalankan test spesifik
php artisan test --filter=AuthTest
```

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`

## Contributing

1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Troubleshooting

### Masalah Umum

1. **Error 500**
   - Periksa log di `storage/logs/laravel.log`
   - Pastikan permission folder storage dan bootstrap/cache

2. **Database Connection Error**
   - Periksa konfigurasi database di `.env`
   - Pastikan database server berjalan

3. **Seeder Error**
   - Jalankan `php artisan migrate:fresh --seed`
   - Periksa urutan seeder di `DatabaseSeeder.php`

## License

Proyek ini menggunakan [MIT license](https://opensource.org/licenses/MIT).

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
