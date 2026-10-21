# Coffee Shop

Aplikasi kasir dan katalog menu berbasis Laravel.

## Persyaratan

- PHP 8.2 atau lebih baru dengan ekstensi `pdo_sqlite`, `mbstring`, dan `openssl`
- Composer
- Node.js dan npm

Untuk Laragon, pastikan ekstensi `pdo_sqlite` aktif pada versi PHP yang digunakan.
Setup awal memakai SQLite; MySQL tidak perlu dipasang untuk menjalankan aplikasi
secara lokal.

## Menjalankan setelah clone

Jalankan perintah dari folder proyek. `.env` dan database lokal tidak disimpan di
Git, jadi setiap laptop membuat konfigurasinya sendiri.

### Windows (PowerShell / Laragon)

```powershell
Copy-Item .env.example .env
if (-not (Test-Path database\database.sqlite)) {
    New-Item database\database.sqlite -ItemType File | Out-Null
}
composer install
php artisan key:generate
php artisan migrate --seed
npm ci
npm run build
php artisan serve
```

Buka `http://localhost:8000`.

### macOS / Linux

```sh
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate --seed
npm ci
npm run build
php artisan serve
```

Buka `http://localhost:8000`.

## Akun administrator

Untuk membuat akun administrator, isi `ADMIN_NAME`, `ADMIN_EMAIL`, dan
`ADMIN_PASSWORD` di `.env` sebelum menjalankan `php artisan migrate --seed`.
Jika seeder sudah pernah dijalankan, jalankan `php artisan db:seed` setelah
mengubah nilai tersebut.

Dalam `APP_ENV=local`, login menerima input apa pun dan halaman admin terbuka
untuk pengembangan. Jangan gunakan mode lokal atau `APP_DEBUG=true` pada server
publik. Untuk penggunaan produksi, siapkan server dan database sendiri serta
atur `.env` produksi secara aman.

## Database dan data

Setiap laptop memiliki database SQLite sendiri di `database/database.sqlite`.
File itu tidak disalin dari Git dan data tidak tersinkron otomatis antar laptop.
Jika ingin memindahkan data, buat dan pulihkan backup database secara terpisah.
Untuk memakai MySQL, ubah `DB_CONNECTION`, host, port, nama database, dan
kredensial di `.env` sesuai server MySQL yang tersedia.

Jika memilih `SESSION_DRIVER=database` atau `CACHE_STORE=database`, tabel yang
diperlukan akan dibuat oleh migrasi. Di produksi, jalankan scheduler Laravel
setiap menit (`php artisan schedule:run`) untuk tugas terjadwal.
