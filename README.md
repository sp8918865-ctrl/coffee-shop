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
php artisan serve --host=0.0.0.0 --port=8000
```

Buka `http://localhost:8000` di komputer. Dari ponsel pada Wi-Fi yang sama,
buka `http://ALAMAT-IP-KOMPUTER:8000` dan izinkan PHP/Laragon pada Windows
Firewall jika diminta.

### Akses menu pelanggan dari ponsel

Menu pelanggan dapat dibuka menggunakan kuota internet ponsel maupun Wi-Fi.
Untuk penggunaan di luar jaringan lokal, aplikasi harus dipasang pada server
yang dapat dijangkau dari internet dan `PUBLIC_MENU_URL` harus diisi dengan URL
publiknya, misalnya `https://menu.contoh.id`. Setelah itu, QR menu yang dibuat
di panel admin akan menggunakan URL tersebut.

Saat pengembangan lokal, `PUBLIC_MENU_URL` dapat diisi dengan alamat IP komputer
yang bisa dijangkau dari jaringan Wi-Fi yang sama. Alamat `localhost` tidak
dapat dibuka dari ponsel.

### macOS / Linux

```sh
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate --seed
npm ci
npm run build
php artisan serve --host=0.0.0.0 --port=8000
```

Buka `http://localhost:8000`.

## Akun administrator

Untuk membuat akun administrator dan barista, isi `ADMIN_NAME`, `ADMIN_USERNAME`,
`ADMIN_PASSWORD`, `BARISTA_NAME`, `BARISTA_USERNAME`, dan `BARISTA_PASSWORD` di
`.env` sebelum menjalankan `php artisan migrate --seed`. Username admin dan
barista harus berbeda.
Jika seeder sudah pernah dijalankan, jalankan `php artisan db:seed` setelah
mengubah nilai tersebut.

Login admin/owner tersedia di `/login/admin`, sedangkan login barista tersedia
di `/login/barista`. Keduanya menggunakan sesi terpisah sehingga admin dan
barista dapat membuka panel masing-masing bersamaan pada browser yang sama.
Keluar dari satu panel tidak mengeluarkan akun dari panel lainnya.

## Database dan data

Setiap laptop memiliki database SQLite sendiri di `database/database.sqlite`.
File itu tidak disalin dari Git dan data tidak tersinkron otomatis antar laptop.
Jika ingin memindahkan data, buat dan pulihkan backup database secara terpisah.
Untuk memakai MySQL, ubah `DB_CONNECTION`, host, port, nama database, dan
kredensial di `.env` sesuai server MySQL yang tersedia.

Jika memilih `SESSION_DRIVER=database` atau `CACHE_STORE=database`, tabel yang
diperlukan akan dibuat oleh migrasi. Di produksi, jalankan scheduler Laravel
setiap menit (`php artisan schedule:run`) untuk tugas terjadwal.
