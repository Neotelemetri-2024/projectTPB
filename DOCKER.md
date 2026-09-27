# Panduan Docker Portal TPB

Dokumen ini menjelaskan penggunaan Docker untuk development dan production.

## Ringkasan port

| Mode | URL aplikasi | Vite/HMR | File Compose |
|---|---|---|---|
| Development | `http://localhost:8001` | `http://localhost:5173` | `compose.dev.yaml` |
| Production | `http://localhost:8001` | Tidak digunakan | `compose.yaml` |

Port `5173` hanya digunakan untuk asset frontend dan Hot Module Replacement saat development. Aplikasi Laravel tetap dibuka melalui port `8001`.

## Persiapan

Pastikan Docker Desktop aktif:

```powershell
docker --version
docker compose version
```

Jika belum ada environment:

```powershell
Copy-Item .env.example .env
```

Isi `.env`, terutama `APP_KEY`, koneksi database, dan URL:

```dotenv
APP_URL=http://localhost:8001
DB_HOST=host.docker.internal
```

Jangan commit `.env` production ke repository.

## Development

Development memakai source code dari host melalui bind mount. Perubahan PHP, Blade, CSS, dan JavaScript dapat terbaca tanpa rebuild image.

Pada Windows, bind mount Docker Desktop tetap lebih lambat daripada filesystem Linux. Konfigurasi development mengaktifkan OPcache dengan validasi timestamp agar request PHP lebih ringan. Performa terbaik diperoleh jika repository berada di filesystem WSL2, bukan folder Windows seperti `C:\Project`.

### Menjalankan

```powershell
docker compose -f compose.dev.yaml up -d
```

Buka aplikasi di:

```
http://localhost:8001
```

Vite berjalan di port `5173` dan biasanya tidak perlu dibuka langsung.

### Status dan log

```powershell
docker compose -f compose.dev.yaml ps
docker compose -f compose.dev.yaml logs -f portaltpb-app-dev
docker compose -f compose.dev.yaml logs -f portaltpb-vite-dev
```

Pada startup pertama, tunggu `composer install` dan `npm install` selesai.

### Perintah Laravel

```powershell
docker compose -f compose.dev.yaml exec portaltpb-app-dev php artisan migrate
docker compose -f compose.dev.yaml exec portaltpb-app-dev php artisan optimize:clear
docker compose -f compose.dev.yaml exec portaltpb-app-dev php artisan test
```

### Menghentikan development

```powershell
docker compose -f compose.dev.yaml down
```

Volume dependency tidak ikut dihapus, sehingga startup berikutnya lebih cepat. Untuk mengulang instalasi dependency:

```powershell
docker compose -f compose.dev.yaml down -v
docker compose -f compose.dev.yaml up -d
```

Perubahan source code biasa tidak perlu rebuild. Rebuild hanya jika mengubah `Dockerfile.dev`, ekstensi PHP, Nginx, atau dependency image:

```powershell
docker compose -f compose.dev.yaml up -d --build
```

Setelah perubahan pada `Dockerfile.dev` atau `docker/php/dev.ini`, lakukan rebuild sekali:

```powershell
docker compose -f compose.dev.yaml down
docker compose -f compose.dev.yaml up -d --build
```

Untuk performa terbaik di Windows, clone repository ke WSL2 lalu jalankan Compose dari terminal WSL:

```bash
mkdir -p ~/workspace
cd ~/workspace
git clone <URL-REPOSITORY> projectTPB
cd projectTPB
docker compose -f compose.dev.yaml up -d
```

## Production

Production memakai `compose.yaml`. Source code disalin ke image dan tidak di-mount dari host. Setiap perubahan kode memerlukan build image baru.

### Konfigurasi dan menjalankan

Gunakan environment production:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://SERVER_IP:8001
```

Jalankan:

```bash
docker compose -f compose.yaml up -d --build
```

Periksa:

```bash
docker compose -f compose.yaml ps
docker compose -f compose.yaml logs --tail=200 portaltpb-app
```

Aplikasi tersedia di `http://SERVER_IP:8001`.

### Update kode dari Git

```bash
git pull
docker compose -f compose.yaml up -d --build
docker compose -f compose.yaml exec portaltpb-app php artisan optimize:clear
```

Build production memasang dependency Composer, membangun asset Vite, dan membuat image baru. Jangan menjalankan `npm run dev` di production.

### Restart dan stop

```bash
docker compose -f compose.yaml restart
docker compose -f compose.yaml down
```

## Konflik port

Hanya satu container yang boleh memakai host port `8001`. Jangan menjalankan development dan production bersamaan.

Jika port sudah digunakan:

```powershell
docker ps --format "table {{.Names}}\t{{.Ports}}"
docker compose -f compose.dev.yaml down
docker compose -f compose.yaml down
```

Lalu jalankan salah satu mode saja.

## Troubleshooting

### Container hidup, tetapi halaman belum bisa dibuka

```powershell
docker compose -f compose.dev.yaml logs -f portaltpb-app-dev
```

Pada first start, tunggu `composer install` selesai dan PHP-FPM/Nginx aktif.

### Frontend tidak berubah

Pastikan Vite berjalan dan lakukan hard refresh (`Ctrl+F5`):

```powershell
docker compose -f compose.dev.yaml logs -f portaltpb-vite-dev
```

Jika dependency rusak:

```powershell
docker compose -f compose.dev.yaml down -v
docker compose -f compose.dev.yaml up -d
```

### Perubahan production tidak terlihat

```bash
docker compose -f compose.yaml up -d --build
docker compose -f compose.yaml exec portaltpb-app php artisan optimize:clear
```

Untuk development, gunakan service `portaltpb-app-dev` dan file `compose.dev.yaml`.

## Keamanan production

- Gunakan `APP_DEBUG=false`.
- Lindungi file `.env`.
- Gunakan HTTPS melalui reverse proxy untuk akses publik.
- Jangan commit password database atau `APP_KEY`.
- Backup database dan folder `storage`.
