# REST API Warung Luwak

Semua endpoint menggunakan prefix `/api/v1`, menerima dan mengembalikan JSON. Buka `/api` atau `/api/v1` untuk melihat indeks endpoint. Sertakan `Accept: application/json`. Endpoint yang memerlukan token menerima `Authorization: Bearer <token>`.

## Akses pelanggan melalui QR

Pindai QR **REST API** pada halaman admin **QR Menu**. Aplikasi pelanggan membuka URL bertanda tangan pada QR dan menerima token Bearer pelanggan yang berlaku 12 jam. Jangan menyimpan token di URL atau membagikannya.

| Method | Endpoint | Akses |
|---|---|---|
| `GET` | `/customer/qr-access` | URL QR bertanda tangan |
| `GET` | `/customer/menu` | Token pelanggan |
| `GET` | `/customer/tables` | Token pelanggan |
| `POST` | `/customer/orders` | Token pelanggan |
| `GET` | `/customer/orders/{tracking_token}` | Token pelanggan |
| `POST` | `/customer/auth/logout` | Token pelanggan |

Contoh membuat pesanan:

```json
{
  "customer_name": "Ayu",
  "customer_phone": "081234567890",
  "order_type": "take_away",
  "payment_method": "qris",
  "notes": "Tidak pakai gula",
  "items": [
    { "product_id": 1, "quantity": 2 }
  ]
}
```

`order_type` menerima `dine_in` atau `take_away`; pesanan `dine_in` memerlukan `table_id` dari `/customer/tables`. `payment_method` menerima `cash` atau `qris`. Harga dihitung dari database, bukan dari nilai yang dikirim klien. Respons `201` berisi `tracking_token` untuk memeriksa status pesanan.

## Akses admin

Masuk dengan akun admin/owner. Token akses berlaku 30 hari dan dapat dicabut di endpoint logout.

```http
POST /api/v1/auth/login
Content-Type: application/json
Accept: application/json
```

```json
{
  "username": "admin",
  "password": "rahasia",
  "device_name": "admin-mobile"
}
```

| Method | Endpoint | Keterangan |
|---|---|---|
| `POST` | `/auth/login` | Masuk admin dan mendapatkan token |
| `GET` | `/admin/auth/me` | Profil admin |
| `POST` | `/admin/auth/logout` | Mencabut token saat ini |
| `GET` | `/admin/dashboard` | Ringkasan pendapatan, pesanan, menu, dan bahan kritis |
| `GET`, `POST` | `/admin/products` | Daftar dan tambah menu |
| `PUT`, `DELETE` | `/admin/products/{product}` | Ubah atau hapus menu |
| `GET`, `POST` | `/admin/categories` | Daftar dan tambah kategori |
| `PATCH`, `DELETE` | `/admin/categories/{category}` | Ubah atau hapus kategori |
| `GET`, `POST` | `/admin/ingredients` | Daftar dan tambah bahan |
| `PUT`, `DELETE` | `/admin/ingredients/{ingredient}` | Ubah atau hapus bahan |
| `GET`, `POST` | `/admin/tables` | Daftar dan tambah meja |
| `PUT`, `DELETE` | `/admin/tables/{table}` | Ubah atau hapus meja |
| `GET` | `/admin/orders` | Daftar pesanan; filter opsional `?status=pending` |
| `GET`, `DELETE` | `/admin/orders/{order}` | Detail atau hapus pesanan |
| `PATCH` | `/admin/orders/{order}/status` | Ubah status pesanan |

Status pesanan yang dapat digunakan: `pending`, `received`, `in_progress`, `ready`, `completed`, `cancelled`. Endpoint admin memerlukan token dari login admin, bukan token QR pelanggan.

## Menjalankan migrasi

Setelah memperbarui aplikasi, jalankan:

```sh
php artisan migrate
```

Sanctum menyimpan token admin yang sudah di-hash. Token QR pelanggan juga disimpan dalam bentuk hash dan kedaluwarsa otomatis setelah 12 jam.
