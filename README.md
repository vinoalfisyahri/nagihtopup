<p align="center">
  <!-- ANIMASI JUDUL UTAMA -->
  <svg viewBox="0 0 800 120" width="100%">
    <style>
      .text-title {
        font-family: 'Segoe UI', Ubuntu, Sans-Serif;
        font-weight: 800;
        font-size: 64px;
        fill: none;
        stroke: #00E676;
        stroke-width: 2px;
        stroke-dasharray: 8% 25%;
        stroke-dashoffset: 0%;
        animation: textanimation 6s linear infinite;
      }
      @keyframes textanimation {
        100% {
          stroke-dashoffset: 33%;
        }
      }
    </style>
    <text x="50%" y="65%" text-anchor="middle" class="text-title">NAGIHTOPUP</text>
  </svg>
</p>

<p align="center">
  <img src="https://shields.io" alt="Maintained">
  <img src="https://shields.io" alt="Laravel">
  <img src="https://shields.io" alt="License">
</p>

---

## 🚀 Tentang Proyek
**nagihtopup** adalah platform aplikasi berbasis web modern yang dibangun menggunakan framework Laravel. Proyek ini dirancang khusus untuk memberikan pengalaman transaksi top-up game dan layanan digital yang cepat, aman, dan andal.

---

## 👥 Anggota Kelompok
Proyek ini dikembangkan dan dikelola dengan bangga oleh kelompok kami:

* 🌟 **Vino**
* 🌟 **Abe**
* 🌟 **Zafran**
* 🌟 **Raihan**

---

## 🛠️ Langkah Instalasinya

Ikuti panduan cepat ini untuk menjalankan aplikasi **nagihtopup** di komputer lokal Anda:

### 1. Gandakan Repositori & Masuk Folder
```bash
git clone <url-repositori-anda>
cd nagihtopup
```

### 2. Pasang Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Environment File
Salin file template lingkungan untuk mengaktifkan konfigurasi sistem:
* **Linux / macOS**:
  ```bash
  cp .env.example .env
  ```
* **Windows (CMD / PowerShell)**:
  ```bash
  copy .env.example .env
  ```

### 4. Buat Application Key Keamanan
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi Database
*Pastikan Anda sudah mengonfigurasi nama database di dalam file `.env` baru Anda.*
```bash
php artisan migrate
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Buka browser Anda dan akses halaman web melalui tautan `http://127.0.0.1:8000`.

---

<p align="center">
  <sub>Dibuat dengan ❤️ oleh Kelompok Vino, Abe, Zafran, & Raihan.</sub>
</p>
