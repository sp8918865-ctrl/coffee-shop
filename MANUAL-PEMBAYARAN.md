# Panduan Pembayaran

Panduan ini menjelaskan pembayaran di aplikasi Coffee Shop untuk admin, staf operasional, dan pelanggan.

## Metode pembayaran yang tersedia

- **Tunai** — pelanggan membayar di kedai. Pembayaran baru berstatus lunas setelah admin atau staf mencatat uang yang diterima.
- **Transfer bank** — pelanggan memilih bank pengirim saat checkout, lalu meminta detail rekening tujuan kepada staf. Staf memeriksa dana masuk dan mengonfirmasi pembayaran secara manual.
- **QRIS statis** — dapat digunakan jika gambar QRIS resmi usaha telah disimpan pada disk `public` dan path-nya diatur melalui `QRIS_STATIC_IMAGE`. Pelanggan memindai QR dan membayar sesuai total; staf tetap memverifikasi dan mengonfirmasi pembayaran secara manual. Jika QRIS belum dikonfigurasi, pelanggan dapat mengganti metode dari halaman status pesanan.

Status pembayaran berbeda dari status pesanan. Pesanan dapat sedang diracik atau siap, sementara pembayarannya masih menunggu konfirmasi.

## Struktur antarmuka

- **Pelanggan:** tampilan berorientasi ponsel dengan header Warung Luwak, tombol keranjang, konten utama, dan navigasi bawah **Menu**, **Keranjang**, serta **Pesanan**. Checkout menampilkan ringkasan keranjang dan total, detail pelanggan serta pesanan, pilihan metode pembayaran, lalu tombol **Buat pesanan**.
- **Admin:** panel dengan navigasi di sisi kiri pada layar lebar (berubah menjadi navigasi mendatar pada layar kecil), identitas akun dan judul halaman di bagian atas, lalu konten. Buka **Pesanan** untuk melihat kartu pesanan yang dikelompokkan menurut status; informasi pembayaran dan tindakan terkait ada di kartu masing-masing.
- **Staf operasional:** menggunakan tata letak panel yang sama dengan admin, tetapi dengan menu sesuai akses barista. Di halaman **Pesanan**, tindakan pembayaran tunai, konfirmasi transfer, dan pembuatan bon tersedia pada kartu pesanan.

## Validasi formulir di browser

Validasi berikut membantu mencegah kesalahan sebelum formulir dikirim. Pesan kesalahan biasanya muncul di dekat kolom atau sebagai pemberitahuan browser.

- **Checkout pelanggan:** nama wajib diisi (maksimal 255 karakter); nomor WhatsApp boleh kosong (maksimal 30 karakter). Meja menjadi wajib jika memilih makan di tempat. Jika memilih transfer, bank pengirim wajib dipilih; pilihan **Bank lainnya** juga meminta nama bank. Pilihan dan kolom yang wajib menyesuaikan metode pembayaran serta jenis pesanan. Metode yang masih menunggu pembayaran dapat diganti dari halaman status pesanan.
- **Pembayaran tunai admin/staf:** jumlah uang wajib berupa angka rupiah bulat, minimal sebesar total pesanan dan maksimal Rp 9.999.999.999. Kolom yang valid disimpan otomatis saat nilai berubah atau kolom ditinggalkan; kembalian ditampilkan setelah penyimpanan berhasil. Nilai di bawah total tidak disimpan.
- **Konfirmasi transfer:** tombol menampilkan dialog konfirmasi browser. Dialog tersebut bukan pemeriksaan transaksi bank; staf tetap harus memastikan dana benar-benar masuk sebelum menyetujui.

Validasi browser bukan pengganti validasi server dan dapat dilewati oleh klien yang dimodifikasi. Server tetap memvalidasi data ketika pesanan atau pembayaran dikirim; ikuti pesan kesalahan yang ditampilkan aplikasi jika data ditolak.

## Admin

1. Masuk melalui halaman **Login Admin** (`/login/admin`) menggunakan akun berperan admin atau owner.
2. Buka **Pesanan** (`/admin/pesanan`) dan temukan pesanan berdasarkan nomor pesanan, nama pelanggan, atau total.
3. Proses pembayaran sesuai metode:
   - **Tunai:** masukkan jumlah uang yang benar-benar diterima. Nilainya harus sama dengan atau lebih besar dari total pesanan. Sistem menyimpan pembayaran dan menghitung kembalian; pastikan keterangan tersimpan sebelum menutup halaman.
   - **Transfer bank:** cocokkan transfer yang masuk dengan jumlah pesanan terlebih dahulu, lalu pilih **Konfirmasi transfer masuk**. Tombol ini hanya muncul untuk pembayaran transfer yang belum lunas.
   - **QRIS statis:** cocokkan dana masuk sebelum mengonfirmasi secara manual. QR statis tidak mengirim konfirmasi otomatis.
4. Pilih **Buat bon** untuk melihat atau mencetak bon. Bon menampilkan metode, status pembayaran, dan—untuk tunai—uang diterima serta kembalian.

Pastikan nomor pesanan dan nominal cocok sebelum mengonfirmasi transfer. Aplikasi belum terhubung ke penyedia pembayaran dan tidak menyediakan rekening tujuan; detail rekening harus diberikan oleh kedai melalui staf.

## Staf operasional

1. Masuk melalui halaman **Login Barista** (`/login/barista`) dengan akun berperan **barista**.
2. Buka **Pesanan** (`/barista/pesanan`) dan temukan pesanan yang perlu dibayar atau dikonfirmasi.
3. Untuk pembayaran **tunai**, masukkan uang yang diterima. Pastikan jumlah minimal sebesar total pesanan; sistem menghitung kembalian dan menyimpan pembayaran.
4. Untuk **transfer**, periksa dana masuk dan kecocokannya dengan pesanan, lalu pilih **Konfirmasi transfer masuk**.
5. Untuk **QRIS statis**, cocokkan dana masuk sebelum mengonfirmasi secara manual. Jika QRIS belum dikonfigurasi, minta pelanggan mengganti metode pembayaran pada halaman status pesanan.
6. Pilih **Buat bon** jika pelanggan memerlukan bon cetak.

Panel operasional pembayaran saat ini hanya menerima akun berperan barista. Opsi peran **Kasir** yang tersedia dalam pengelolaan staf belum memiliki alur login panel tersendiri.

## Pelanggan

1. Pindai QR menu dari kedai, pilih menu, lalu lanjutkan ke checkout.
2. Isi nama, pilih makan di tempat atau dibawa pulang, dan pilih meja jika makan di tempat.
3. Pilih metode pembayaran:
   - **Tunai:** pilih ini jika akan membayar langsung kepada staf di kedai.
   - **Transfer bank:** pilih bank yang akan digunakan. Jika memilih **Bank lainnya**, masukkan nama bank. Setelah pesanan dibuat, minta detail rekening tujuan dari staf, transfer sesuai total, lalu tunggu staf memverifikasi pembayaran.
   - **QRIS:** jika QRIS resmi tersedia di halaman status pesanan, pindai dan bayar tepat sesuai total. Jika tidak tersedia, pilih **Ganti metode pembayaran** untuk mengganti ke tunai atau transfer.
4. Tekan **Buat pesanan**. Simpan nomor pesanan dan halaman status yang terbuka setelah checkout. Selama pembayaran belum dikonfirmasi, metode pembayaran dapat diganti pada halaman tersebut.
5. Pada bagian bon pembayaran, periksa apakah status masih **Belum dibayar** atau sudah **Lunas**. Pembayaran tunai dan transfer menunggu konfirmasi staf. Halaman status memperbarui informasi pesanan secara otomatis setiap 30 detik.

QR yang tercetak pada bon untuk melacak status pesanan, bukan QR untuk membayar. Jika status pembayaran belum berubah setelah menyerahkan uang atau melakukan transfer, tunjukkan nomor pesanan kepada staf dan jangan mengirim pembayaran kedua sebelum mendapat kepastian.

## Catatan penting

- QRIS statis tidak memproses konfirmasi otomatis. Status lunas hanya dicatat oleh staf/admin setelah memeriksa dana masuk.
- Jangan menganggap status pesanan **Selesai** sebagai bukti pembayaran; periksa status pembayaran pada bon.
- Simpan nomor pesanan untuk memudahkan staf memeriksa transaksi.
