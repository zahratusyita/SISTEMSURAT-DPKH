# 📬 SIPAS — Sistem Informasi Pengelolaan Administrasi Surat

**SIPAS** adalah aplikasi web berbasis **Laravel 11** untuk mengelola administrasi surat menyurat instansi/organisasi secara digital. Aplikasi ini mencakup pencatatan **Surat Masuk** dan **Surat Keluar** lengkap dengan fitur import/export Excel, upload lampiran, statistik dashboard, dan manajemen multi-akun admin.

---

## ✨ Fitur Utama

### 🔐 Autentikasi
- Login & Logout dengan sistem sesi yang aman
- Fitur **Remember Me** untuk sesi yang lebih panjang
- Seluruh halaman dilindungi middleware autentikasi

### 📊 Dashboard
- **Statistik ringkas**: total surat masuk & keluar (bulan ini, tahun ini, keseluruhan)
- **Grafik surat bulanan** (per tahun berjalan)
- **5 aktivitas surat terbaru** (masuk & keluar, diurutkan berdasarkan tanggal)

### 📥 Surat Masuk
Pengelolaan data surat masuk secara lengkap (CRUD):

| Field | Keterangan |
|---|---|
| Tanggal Masuk | Kapan surat diterima |
| Nomor Urut / Agenda | Rekomendasi format `001/IV/2026`, dapat diedit |
| Alamat Pengirim | Asal surat |
| Tanggal Surat | Tanggal yang tertera di surat |
| Nomor Surat | Nomor unik dari pengirim |
| Perihal | Maksud / isi surat |
| Tujuan Disposisi | Unit/divisi penerima tindak lanjut |
| Lampiran | Upload file (PDF, JPG, PNG, WEBP — maks. 10 MB) |

**Fitur tambahan:**
- 🔍 **Pencarian** berdasarkan perihal, nomor surat, nomor urut, pengirim, disposisi
- 📅 **Filter** per bulan dan tahun
- 📤 **Export Excel** sesuai filter aktif
- 📥 **Import Excel** dengan pratinjau data sebelum konfirmasi (validasi duplikat & baris tidak valid)
- 🔄 **Reset data** dengan konfirmasi; ditolak jika surat masuk masih mempunyai balasan

### 📤 Surat Keluar
Pengelolaan data surat keluar secara lengkap (CRUD):

| Field | Keterangan |
|---|---|
| Tanggal Keluar | Kapan surat dikirim |
| Nomor Urut / Agenda | Rekomendasi format `001/IV/2026`, dapat diedit |
| Alamat Penerima | Tujuan surat |
| Tanggal Surat | Tanggal yang tertera di surat |
| Nomor Surat | Nomor unik surat |
| Perihal | Maksud / isi surat |
| Asal | Unit/divisi pembuat surat |
| Lampiran | Upload file (PDF, JPG, PNG, WEBP — maks. 10 MB) |

**Fitur tambahan:** Sama seperti Surat Masuk (pencarian, filter, export, import, reset).

### 👤 Manajemen Akun (Profil)
- Edit **nama** dan **email** akun sendiri
- Ubah **password** (dengan verifikasi password lama)
- **Tambah admin** baru (multi-akun)
- **Nonaktifkan akun** admin lain: sesi dicabut, identitas pencatat surat dipertahankan, akun sendiri dan admin terakhir dilindungi

---

## 💻 Tech Stack

| Layer | Teknologi |
|---|---|
| Backend Framework | Laravel 11 (PHP ^8.2) |
| Frontend Bundler | Vite |
| CSS Framework | Tailwind CSS |
| Database (default) | MySQL (InnoDB, utf8mb4) |
| Import / Export | maatwebsite/excel ^3.1 |

---

## 🚀 Menjalankan Aplikasi dengan MySQL

Instalasi lokal ini memakai MySQL Community Server 8.4.10, database `sipas_db`, serta akun yang sudah dipindahkan dari SQLite. Database memiliki 11 tabel dan 6 foreign key.

```powershell
.\Start-Localhost.ps1
```

Untuk pengembangan frontend dengan Vite:

```powershell
.\Start-Localhost.ps1 -WithVite
```

Akses aplikasi di **http://127.0.0.1:8000**. Skrip memakai PHP pada PATH atau PHP XAMPP, menjalankan server MySQL lokal jika diperlukan, dan memeriksa skema sebelum menyalakan Laravel. Aset frontend disiapkan dengan `npm run build`.

Lihat [panduan MySQL](docs/panduan-mysql.md) untuk instalasi pada komputer lain, konfigurasi database, pengujian, backup, dan pemulihan. Folder `.local`, `.env`, `.env.testing`, dan `backups` tidak dikirim melalui Git.

### Relasi Data dan Surat Balasan

- `surat_masuk.created_by` / `updated_by` merujuk `users.id`.
- `surat_keluar.created_by` / `updated_by` merujuk `users.id`.
- `surat_keluar.surat_masuk_id` menghubungkan balasan dengan surat masuk asal secara opsional.
- `sessions.user_id` merujuk `users.id` dan dapat kosong untuk pengunjung.
- Detail surat menampilkan pencatat, pengubah terakhir, dan hubungan surat balasan.
- Penghapusan akun menggunakan soft delete; identitas pencatat tetap tersimpan pada surat.
- Surat masuk yang memiliki balasan dilindungi dari penghapusan/reset.

ERD aktual tersedia pada [ERD tabel utama](database/ERD-MySQL-utama.svg), [ERD lengkap](database/ERD-MySQL-lengkap.svg), dan [skema SQL](database/schema-mysql.sql).

### Pengujian

```powershell
& C:\xampp\php\php.exe vendor\bin\phpunit --testdox
npm run build
```

PHPUnit dibatasi pada database `sipas_test` dengan akun database tersendiri. Pengujian browser memakai `sipas_ui_test` sehingga tidak mengganggu database operasional atau proses PHPUnit.

---

## 📁 Struktur Modul Utama

```
app/
├── Http/Controllers/
│   ├── AuthController.php            # Login & Logout
│   ├── DashboardController.php       # Statistik & grafik
│   ├── SuratMasukController.php      # CRUD Surat Masuk
│   ├── SuratMasukImportController.php # Import Excel Surat Masuk
│   ├── SuratKeluarController.php     # CRUD Surat Keluar
│   ├── SuratKeluarImportController.php # Import Excel Surat Keluar
│   └── ProfileController.php         # Manajemen akun admin
resources/views/
├── dashboard.blade.php               # Halaman dashboard
├── surat_masuk/                      # Views surat masuk
├── surat_keluar/                     # Views surat keluar
├── profile/                          # Halaman profil & manajemen user
└── auth/                             # Halaman login
```

---

## 🌐 Deploy ke Server (Production dengan MySQL)

Saat deploy ke server hosting/VPS, ganti konfigurasi database dari SQLite ke **MySQL**.

### 1. Buat Database MySQL di Server

Masuk ke MySQL lalu buat database baru:
```sql
CREATE DATABASE sipas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Sesuaikan File `.env` di Server

Ubah bagian database di `.env` menjadi:
```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipas_db
DB_USERNAME=user_mysql_anda
DB_PASSWORD=password_mysql_anda
```

> **Penting:** Pastikan `APP_DEBUG=false` saat production agar pesan error tidak tampil ke publik.

### 3. Jalankan Migrasi

```bash
php artisan migrate --force
```

Flag `--force` diperlukan karena environment `production` Laravel meminta konfirmasi.

### 4. Optimasi untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### 5. Build Asset Frontend

```bash
npm run build
```

> Di server production, **tidak perlu** menjalankan `npm run dev` atau `php artisan serve`. Server web (Apache/Nginx) langsung mengarahkan ke folder `public/`.

---

### Integritas Database

Database operasional menggunakan MySQL dengan foreign key dan aturan penghapusan yang sesuai. Kolom pembuat/pengubah diisi server; pengguna tidak dapat memalsukan identitas pencatat melalui formulir. Reset data tidak mereset ID internal. Lampiran lama dibersihkan setelah transaksi database berhasil.

Data SQLite lama dapat dipindahkan ke database MySQL kosong melalui `php artisan sipas:transfer-sqlite --dry-run`, kemudian `php artisan sipas:transfer-sqlite`. Prosedur lengkap tersedia dalam panduan MySQL.

---

## 📄 Format Template Import Excel

Untuk fitur **Import Excel**, gunakan kolom berikut (baris pertama adalah header):

**Surat Masuk:**
| tanggal_masuk_surat | nomor_urut | alamat_pengirim | tanggal_surat | nomor_surat | perihal | tujuan_disposisi |
|---|---|---|---|---|---|---|

**Surat Keluar:**
| tanggal_keluar_surat | nomor_urut | alamat_penerima | tanggal_surat | nomor_surat | perihal | asal |
|---|---|---|---|---|---|---|

> Format tanggal: `YYYY-MM-DD` (contoh: `2026-04-25`)

---

**Semoga berhasil dan selamat mengembangkan SIPAS! 🚀**
