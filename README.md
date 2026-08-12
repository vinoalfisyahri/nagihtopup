A. Tabel categories (Game / Layanan)
Menyimpan daftar game atau produk layanan yang tersedia.

Kolom	Tipe Data	Keterangan
id	BigInt (PK)	Primary Key
name	VarChar(100)	Nama Game (contoh: Mobile Legends)
slug	VarChar(100)	URL friendly (mobile-legends)
publisher	VarChar(100)	Penerbit (contoh: Moonton)
target_field_type	VarChar(50)	Jenis input user (contoh: id_only, id_zone)
thumbnail	VarChar(255)	URL/Path gambar game
status	Enum	Status aktif (active, inactive)
B. Tabel products (Item / Nominal Top-Up)
Menyimpan nominal atau item yang dijual untuk tiap game.

Kolom	Tipe Data	Keterangan
id	BigInt (PK)	Primary Key
category_id	BigInt (FK)	Relasi ke categories.id
name	VarChar(100)	Nama Item (contoh: 86 Diamonds)
price	Decimal(12,2)	Harga jual ke konsumen
cost_price	Decimal(12,2)	Harga modal (dari provider API)
provider_code	VarChar(50)	Kode produk dari provider/supplier API
status	Enum	Status ketersediaan (available, empty)
C. Tabel payment_methods (Metode Pembayaran)
Menyimpan metode pembayaran yang didukung (QRIS, VA, E-Wallet, Retail).

Kolom	Tipe Data	Keterangan
id	BigInt (PK)	Primary Key
code	VarChar(50)	Kode metode (contoh: qris, bca_va, gopay)
name	VarChar(100)	Nama metode (contoh: BCA Virtual Account)
type	VarChar(50)	Kategori (e-wallet, virtual_account, convenience_store)
admin_fee	Decimal(10,2)	Biaya admin tambahan
status	Enum	Status aktif (active, inactive)
D. Tabel orders / transactions (Transaksi)
Tabel utama pencatatan setiap proses pembelian.

Kolom	Tipe Data	Keterangan
id	BigInt (PK)	Primary Key
invoice_number	VarChar(50)	Nomor Invoice unik (contoh: INV-20260811-001)
user_id	BigInt (FK)	Relasi ke users.id (Nullable, jika mendukung guest checkout)
product_id	BigInt (FK)	Relasi ke products.id
payment_method_id	BigInt (FK)	Relasi ke payment_methods.id
target_user_id	VarChar(100)	User ID Game pembeli (contoh: 12345678)
target_zone_id	VarChar(50)	Server ID / Zone ID (opsional, contoh: 2105)
phone_number	VarChar(20)	Nomor WhatsApp/HP untuk notifikasi
amount	Decimal(12,2)	Total harga produk + biaya admin
payment_status	Enum	Status pembayaran (pending, paid, failed, expired)
processing_status	Enum	Status top-up (processing, success, failed)
payment_reference	VarChar(100)	Ref / Token dari Payment Gateway (Midtrans/Tripay/dll.)
created_at	Timestamp	Waktu transaksi dibuat
E. Tabel users (Pengguna / Pembeli)
Menyimpan data pembeli terdaftar (jika platform memiliki fitur akun/member).

Kolom	Tipe Data	Keterangan
id	BigInt (PK)	Primary Key
name	VarChar(100)	Nama pengguna
email	VarChar(100)	Email unik
password	VarChar(255)	Hash password
role	Enum	Hak akses (admin, customer)
2. Relasi Antar Tabel (Entity Relationship)
Relationships yang menghubungkan antar tabel di atas adalah sebagai berikut:

categories (1) ─── (N) products

One-to-Many: Satu game (Category) memiliki banyak nominal/item pilihan (Products).

products.category_id merujuk pada categories.id.

products (1) ─── (N) orders

One-to-Many: Satu jenis item (Product) bisa dibeli di banyak transaksi (Orders).

orders.product_id merujuk pada products.id.

payment_methods (1) ─── (N) orders

One-to-Many: Satu metode pembayaran bisa digunakan pada banyak transaksi (Orders).

orders.payment_method_id merujuk pada payment_methods.id.

users (1) ─── (N) orders

One-to-Many (Optional): Satu akun pengguna bisa memiliki banyak riwayat transaksi.

orders.user_id merujuk pada users.id (dibuat Nullable agar transaksi tanpa login tetap bisa dilakukan).

3. Alur Data Transaksi (Workflow Singkat)
Pembeli memilih Game (categories) dan menentukan Item (products).

Pembeli memasukkan ID Game (target_user_id & target_zone_id) serta memilih Metode Pembayaran (payment_methods).

Sistem membuat Invoice Baru di tabel orders dengan payment_status = pending.

Setelah pembeli membayar, Payment Gateway memberikan notifikasi (callback) ke sistem:

Status pembayaran berubah menjadi payment_status = paid.

Sistem otomatis menembak API Provider Top-Up.

Jika sukses, processing_status berubah menjadi success.