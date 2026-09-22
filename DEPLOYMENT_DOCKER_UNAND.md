# Deployment Docker Portal TPB di Server UNAND

Dokumen ini berisi prosedur deployment aplikasi TPB menggunakan satu container aplikasi yang menjalankan Laravel, PHP-FPM, dan Nginx. Database MySQL berjalan sebagai service/container terpisah sesuai VM yang disediakan, bukan di dalam container aplikasi.

## 1. Arsitektur deployment

- Satu container aplikasi: Laravel + PHP-FPM + Nginx.
- Image aplikasi berisi source code aplikasi, dependency Composer, dan asset Vite.
- Source code aplikasi **tidak** di-mount sebagai volume.
- Volume/bind mount hanya digunakan untuk data dan konfigurasi yang perlu bertahan di luar container:
  - `./storage:/var/www/html/storage`
  - `./.env:/var/www/html/.env`
- MySQL berjalan di service/container MySQL pada VM yang disediakan.
- Koneksi database diatur melalui `DB_HOST`, `DB_PORT`, dan kredensial pada `.env`.
- Akses publik sebaiknya melalui HTTPS menggunakan reverse proxy/SSL di depan container.

## 2. Persiapan lokal

Sebelum membangun image, pastikan aplikasi berjalan normal di localhost.

```bash
php artisan serve
```

Buka aplikasi melalui `http://127.0.0.1:8000` atau port lokal yang digunakan. Periksa minimal:

- login dan logout;
- dashboard sesuai role;
- input/edit nilai;
- laporan CPL dan capaian mahasiswa;
- upload/download file;
- import/export Excel;
- asset CSS dan JavaScript;
- koneksi database;
- tidak ada error pada `storage/logs/laravel.log`.

Build asset juga harus berhasil:

```bash
npm install
npm run build
```

## 3. Struktur folder di server

Contoh direktori deployment:

```text
/home/docker/
└── tpb/
    ├── app/ atau source project Laravel
    ├── compose.yaml
    ├── Dockerfile
    ├── docker-entrypoint.sh
    ├── docker/
    │   └── nginx/
    │       └── conf.d/
    │           └── app.conf
    ├── storage/
    ├── .env
    ├── .dockerignore
    ├── composer.json
    ├── composer.lock
    ├── package.json
    └── package-lock.json
```

Untuk project ini, file konfigurasi yang digunakan adalah:

- `compose.yaml`
- `Dockerfile`
- `docker/entrypoint.sh`
- `docker/nginx/conf.d/app.conf`
- `.env`

## 4. Salin project ke server

Buat folder project dan masuk ke dalamnya:

```bash
mkdir -p /home/docker/tpb
cd /home/docker/tpb
```

Salin source code dan file konfigurasi dari komputer developer ke server. Contoh menggunakan `scp`:

```bash
scp -r . docker@SERVER_IP:/home/docker/tpb
```

Atau gunakan Git/SFTP sesuai prosedur tim. Pastikan file `.env` production tidak tertinggal dan permission-nya aman.

Jangan menyalin folder berikut jika tidak diperlukan karena akan dibuat di dalam image:

- `vendor/`
- `node_modules/`
- cache hasil build lama
- file `.env` milik development

## 5. Siapkan `.env` production

Buat atau salin `.env` production di `/home/docker/tpb/.env`.

Contoh bagian penting:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://DOMAIN-TPB
APP_KEY=base64:ISI_APP_KEY_PRODUCTION

DB_CONNECTION=mysql
DB_HOST=HOST_MYSQL_SESUAI_VM
DB_PORT=3306
DB_DATABASE=tpb
DB_USERNAME=USERNAME_DATABASE
DB_PASSWORD=PASSWORD_DATABASE

CACHE_DRIVER=file
SESSION_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
```

Catatan:

- Jangan gunakan `APP_DEBUG=true` di production.
- `APP_KEY` harus tersedia dan jangan diganti sembarangan setelah aplikasi digunakan.
- Jika MySQL berada pada container/VM yang sama dan dapat diakses melalui gateway host, gunakan host yang diberikan oleh administrator. Pada Docker Desktop, biasanya `host.docker.internal`; pada server Linux, gunakan hostname/IP MySQL yang benar.
- Jangan menganggap `127.0.0.1` di dalam container sebagai host server. Di dalam container, `127.0.0.1` menunjuk ke container aplikasi sendiri.
- Pastikan MySQL mengizinkan koneksi dari jaringan/container aplikasi.

Amankan file environment:

```bash
chmod 600 .env
```

## 6. Siapkan storage writable

Folder yang di-mount harus tersedia sebelum container dijalankan:

```bash
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
```

Jika permission belum benar, entrypoint akan mencoba memperbaikinya. Bila masih gagal, sesuaikan permission berdasarkan user web server pada image.

## 7. Validasi konfigurasi Compose

Dari `/home/docker/tpb` jalankan:

```bash
docker compose config
```

Pastikan:

- hanya ada satu service aplikasi;
- tidak ada service MySQL/Redis di file Compose aplikasi;
- port host diarahkan ke port container 80, misalnya `8001:80`;
- mount hanya mencakup `storage` dan `.env`;
- tidak ada error parsing.

## 8. Build dan jalankan container

Gunakan perintah berikut:

```bash
docker compose up -d --build
```

Build akan melakukan hal berikut:

1. install dependency npm;
2. menjalankan `npm run build` untuk membuat `public/build/manifest.json`;
3. install dependency Composer;
4. menyalin source code ke image;
5. menjalankan container dengan Nginx dan PHP-FPM.

Periksa status container:

```bash
docker compose ps
```

Periksa log segera setelah deployment:

```bash
docker compose logs --no-color --tail=200 portaltpb-app
```

Pantau log secara langsung bila diperlukan:

```bash
docker compose logs -f portaltpb-app
```

Jika nama service berbeda pada Compose, gunakan nama service yang terlihat dari `docker compose ps`.

## 9. Verifikasi aplikasi

Jika port di `compose.yaml` adalah `8001:80`, buka:

```text
http://SERVER_IP:8001
```

Periksa:

- halaman login tampil tanpa error Vite manifest;
- file CSS/JavaScript termuat;
- login berhasil;
- koneksi MySQL berhasil;
- dashboard dan laporan dapat dibuka;
- storage/upload dapat digunakan;
- tidak ada error 500.

Cek dari dalam container:

```bash
docker compose exec portaltpb-app php artisan about
docker compose exec portaltpb-app php artisan route:list
docker compose exec portaltpb-app ls -lah /var/www/html/public/build
```

File berikut wajib tersedia setelah build:

```text
/var/www/html/public/build/manifest.json
```

Jika perlu menjalankan migrasi, lakukan setelah backup database dan setelah mendapat persetujuan:

```bash
docker compose exec portaltpb-app php artisan migrate --force
```

## 10. HTTPS dan CDN/API

Production wajib menggunakan HTTPS untuk website, CDN, API, dan asset eksternal.

Rekomendasi:

- pasang reverse proxy Nginx/Traefik/load balancer di depan container;
- pasang sertifikat TLS untuk domain aplikasi;
- arahkan trafik HTTPS ke port aplikasi `8001` atau port internal yang ditentukan;
- gunakan URL `https://` pada `APP_URL`;
- pastikan asset Vite, endpoint API, gambar, font, dan CDN tidak menggunakan HTTP mixed content.

Port `8001` sebaiknya tidak langsung diekspos ke internet bila sudah tersedia reverse proxy. Batasi akses firewall dan expose hanya melalui reverse proxy HTTPS.

## 11. Export dan backup MySQL

Sebelum deployment production, export database dan simpan file dump di folder deployment atau lokasi backup yang disetujui:

```bash
mysqldump -h HOST_MYSQL -P 3306 -u USERNAME -p tpb > tpb-v1.sql
```

Untuk import ke database tujuan:

```bash
mysql -h HOST_MYSQL -P 3306 -u USERNAME -p tpb < tpb-v1.sql
```

Jangan menyimpan password database di command history bila kebijakan server melarangnya. Gunakan prompt password atau mekanisme secret yang disediakan administrator.

## 12. Push image ke Docker Registry UNAND

Login ke registry jika diperlukan:

```bash
docker login docker-registry.unand.ac.id:8888
```

Lihat nama image yang dibuat:

```bash
docker images portaltpb
```

Tag versi awal `v1`:

```bash
docker tag portaltpb:latest docker-registry.unand.ac.id:8888/portaltpb:v1
```

Tag versi `latest`:

```bash
docker tag portaltpb:latest docker-registry.unand.ac.id:8888/portaltpb:latest
```

Push kedua tag:

```bash
docker push docker-registry.unand.ac.id:8888/portaltpb:v1
docker push docker-registry.unand.ac.id:8888/portaltpb:latest
```

Jika image lokal menggunakan nama `portaltpb-app`, gunakan nama tersebut pada perintah `docker tag`:

```bash
docker tag portaltpb-app:latest docker-registry.unand.ac.id:8888/portaltpb:v1
docker tag portaltpb-app:latest docker-registry.unand.ac.id:8888/portaltpb:latest
```

## 13. Informasikan tim production

Setelah push berhasil, production tidak otomatis menerima notifikasi. Kirim informasi berikut kepada tim production:

```text
Aplikasi: TPB
Registry: docker-registry.unand.ac.id:8888/portaltpb
Tag: v1 dan latest
Port aplikasi: 8001
Database: MySQL eksternal pada VM yang disediakan
File database: tpb-v1.sql
Catatan: APP_KEY dan .env production harus disiapkan di server production.
```

## 14. Perintah operasional

Melihat status:

```bash
docker compose ps
```

Melihat log:

```bash
docker compose logs -f portaltpb-app
```

Restart aplikasi:

```bash
docker compose restart portaltpb-app
```

Update image/source:

```bash
docker compose down
docker compose up -d --build
```

Masuk ke container:

```bash
docker compose exec portaltpb-app sh
```

Hentikan aplikasi tanpa menghapus image:

```bash
docker compose down
```

## 15. Checklist deployment

- [ ] Website berjalan normal di localhost.
- [ ] `npm run build` berhasil.
- [ ] Database sudah di-export/backup.
- [ ] Folder `/home/docker/tpb` sudah dibuat.
- [ ] `.env` production sudah diisi dan permission aman.
- [ ] `DB_HOST` mengarah ke MySQL VM yang benar.
- [ ] `docker compose config` tidak error.
- [ ] `docker compose up -d --build` berhasil.
- [ ] `docker compose ps` menunjukkan container running.
- [ ] `docker compose logs` tidak menunjukkan error fatal.
- [ ] `public/build/manifest.json` tersedia di container.
- [ ] Website dapat dibuka melalui port aplikasi.
- [ ] Login dan fitur utama berhasil diuji.
- [ ] HTTPS/reverse proxy sudah dikonfigurasi.
- [ ] Image sudah ditag dan di-push ke registry UNAND.
- [ ] Tim production sudah diberi informasi tag image dan file database.
