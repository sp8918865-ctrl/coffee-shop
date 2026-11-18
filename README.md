 Coffee Shop

Sistem kasir dan katalog menu berbasis Laravel.

Lihat [Panduan Pembayaran](MANUAL-PEMBAYARAN.md) untuk langkah admin, staf, dan pelanggan.

Persyaratan

- PHP 8.2 atau versi lebih baru (pastikan ekstensi pdo_sqlite, mbstring, dan openssl sudah aktif)
- Composer
- Node.js dan npm

Jika Anda menggunakan Laragon, pastikan ekstensi pdo_sqlite sudah diaktifkan pada versi PHP yang sedang digunakan. Setup awal menggunakan SQLite, sehingga Anda tidak perlu menginstal MySQL untuk menjalankan aplikasi secara lokal.

Menjalankan Aplikasi

Jalankan perintah berikut di dalam direktori proyek. Perlu diingat bahwa file .env dan database lokal tidak disertakan dalam Git, jadi Anda perlu melakukan konfigurasi mandiri di setiap perangkat.

Windows (PowerShell / Laragon)

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
php artisan serve --host=0.0.0.