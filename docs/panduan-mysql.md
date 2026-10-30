# SIPAS: menjalankan sistem dengan MySQL

## Lingkungan lokal yang disiapkan

- PHP 8.2+ (komputer ini: C:\xampp\php\php.exe).
- MySQL Community Server 8.4.10 pada .local/mysql-8.4.10-winx64.
- Data server pada .local/mysql-data; tidak menggunakan data MariaDB XAMPP.
- MySQL hanya mendengarkan 127.0.0.1:3306.
- Database operasional sipas_db dengan akun sipas_app.
- Database pengujian sipas_test dengan akun sipas_tester yang tidak memiliki akses ke database operasional.
- Database browser sipas_ui_test dengan akun sipas_ui_tester, terpisah dari database PHPUnit.
- Password di .env/.env.testing dan .local/mysql-credentials.json; berkas tersebut diabaikan Git.

## Menyalakan aplikasi

Di PowerShell pada direktori proyek:

```powershell
.\Start-Localhost.ps1
```

Skrip menyalakan MySQL lokal bila belum aktif, memeriksa koneksi dan 6 foreign key, lalu menyalakan Laravel. Mode ini menggunakan aset hasil npm run build. Untuk mengembangkan CSS/JavaScript dengan Vite:

```powershell
.\Start-Localhost.ps1 -WithVite
```

Buka http://127.0.0.1:8000 dan gunakan akun yang sama seperti sebelum migrasi. Log server tersedia pada .local/. Server perlu dijalankan kembali setelah komputer restart.

Jangan menyalakan database MariaDB XAMPP pada port 3306 bersamaan dengan MySQL SIPAS. Jika memakai MySQL lain, sesuaikan koneksi .env dan skrip startup.

## Setup pada komputer lain

1. Install PHP 8.2+, Composer, Node.js, serta MySQL yang mendukung InnoDB.
2. Jalankan composer install dan npm ci.
3. Buat database sipas_db menggunakan utf8mb4 / utf8mb4_unicode_ci dan akun database tersendiri.
4. Salin .env.example ke .env dan isi kredensial MySQL lokal.
5. Jalankan php artisan key:generate hanya untuk instalasi baru. Saat memulihkan instalasi, gunakan APP_KEY asli.
6. Jalankan php artisan migrate, php artisan storage:link, dan npm run build.
7. Buat akun awal menggunakan php artisan tinker dengan password pilihan sendiri.
8. Jalankan php artisan sipas:health, lalu php artisan serve --host=127.0.0.1 --port=8000.

Skrip Start-Localhost.ps1 di komputer ini memakai server portabel yang sudah disediakan. Folder .local tidak dikirim melalui Git; komputer lain dapat menggunakan server MySQL yang diinstal sendiri.

## Relasi dan perilaku aplikasi

- surat_masuk.created_by / updated_by merujuk users.id.
- surat_keluar.created_by / updated_by merujuk users.id.
- surat_keluar.surat_masuk_id merujuk surat_masuk.id dan boleh kosong.
- sessions.user_id merujuk users.id dan boleh kosong untuk sesi pengunjung.
- Akun dihapus secara soft delete; identitas pembuat surat dipertahankan. Email akun terhapus tetap dicadangkan karena mempunyai unique key.
- Surat masuk yang memiliki balasan tidak dapat dihapus/reset sebelum hubungan balasannya dilepas.
- Pembuat surat diisi server pada tambah/import; pengubah terakhir diisi pada edit.
- Pencatatan pengguna tidak membatasi akses surat berdasarkan pembuat: administrator tetap mengelola arsip bersama.
- updated_by hanya menunjukkan pengubah terakhir, bukan log seluruh riwayat perubahan.
- ID internal tidak direset setelah surat dihapus. Nomor agenda tetap rekomendasi yang dapat diedit, belum penomoran atomik untuk input bersamaan.

## Impor dan ekspor

Impor mendukung template datar Excel/CSV dan agenda hasil ekspor. Sistem mencari sheet berdasarkan header kolom jenis surat yang dipilih, sehingga satu workbook dengan Sheet1 berisi surat masuk dan Sheet2 berisi surat keluar dapat digunakan untuk kedua fitur. Jika ada beberapa sheet yang cocok, nama Surat Masuk/Surat Keluar diprioritaskan tanpa membedakan huruf besar/kecil; jika tidak, dipilih sheet cocok pertama. Nama sheet saja tidak mengalahkan kecocokan isi header. Pratinjau menampilkan nama sheet yang dibaca. Jika tidak ada sheet cocok, sistem menampilkan pesan format yang jelas. Header dengan spasi/baris baru dan cell merge pada format agenda tetap didukung.

Header template masuk:

```text
tanggal_masuk_surat,nomor_urut,alamat_pengirim,tanggal_surat,nomor_surat,perihal,tujuan_disposisi
```

Header template keluar:

```text
tanggal_keluar_surat,nomor_urut,alamat_penerima,tanggal_surat,nomor_surat,perihal,asal,surat_masuk_id
```

surat_masuk_id opsional dan harus merujuk ID surat masuk yang benar. Gunakan tanggal YYYY-MM-DD atau DD/MM/YYYY; nilai tanggal numerik Excel juga didukung. Identitas pembuat/pengubah dari kolom Excel tidak dipakai.

Khusus format agenda surat keluar lama (header bertingkat), tanggal keluar yang kosong menggunakan tanggal surat, sesuai perilaku aplikasi sebelum refaktor. Pratinjau memberi keterangan pada setiap baris yang memakai penggantian ini. Jika tanggal kirim sebenarnya berbeda dari tanggal surat, isi kolom Tanggal Keluar Surat sebelum mengimpor. Tanggal keluar yang diisi tetapi invalid tetap ditolak; baris tanpa kedua tanggal juga ditolak. Template datar tetap mewajibkan tanggal keluar eksplisit. Header agenda yang digabung sampai tiga baris dilewati seluruhnya, sehingga baris "Tanggal / Nomor / Perihal" tidak dianggap surat.

Pratinjau belum menulis surat ke database. Konfirmasi terikat pada pengguna/sesi dan hanya dapat dipakai sekali. Baris invalid dan duplikat dilewati. Format ekspor agenda tetap seperti sebelumnya dan tidak membawa hubungan balasan; hubungan tersebut diisi lewat form atau template datar yang eksplisit.

## Pengujian

Siapkan .env.testing pada komputer lain dengan kredensial sipas_tester dan database sipas_test. APP_KEY diperlukan untuk pengujian sesi/tampilan.

```powershell
& C:\xampp\php\php.exe vendor\bin\phpunit --testdox
npm run build
```

TestCase menolak koneksi selain MySQL sipas_test sebelum RefreshDatabase membersihkan tabel. Akun sipas_tester juga dibatasi pada sipas_test. Pengujian tidak boleh dijalankan menggunakan akun/database operasional.

Pengujian browser pada komputer ini memakai Chrome dan Playwright dari runtime lokal. Siapkan akun uji lalu jalankan router pada terminal terpisah:

```powershell
& C:\xampp\php\php.exe tools/seed_ui_test.php
& C:\xampp\php\php.exe -S 127.0.0.1:8001 -t public tools/ui_router.php
```

Pada terminal lain, jalankan `node tools/browser_smoke.cjs`. Router menyuntikkan koneksi sipas_ui_test dan menyajikan gambar OCR khusus pengujian dari .local; tidak menambahkan endpoint uji ke aplikasi operasional. Skrip browser membuat gambar uji sendiri dan menghasilkan JSON, screenshot, serta unduhan Excel di .local. Pada komputer lain, sesuaikan lokasi Playwright/Chrome dan siapkan database serta kredensial pengujian terlebih dahulu. Database sipas_ui_test tidak dibersihkan oleh PHPUnit.

## Backup, transfer, dan pemulihan

Backup sebelum migrasi tersedia pada backups/ dalam direktori proyek. Backup mencakup database SQLite konsisten, konfigurasi lokal, dan storage/app. Jangan memasukkan backup/kredensial ke Git atau laporan.

Command transfer hanya menerima tujuan MySQL dengan tabel utama kosong:

```powershell
php artisan sipas:transfer-sqlite --source="path/database.sqlite" --dry-run
php artisan sipas:transfer-sqlite --source="path/database.sqlite"
```

Command mempertahankan ID, timestamp, password hash, dan metadata lampiran serta memverifikasi nilai hasil transfer. Akun dipindahkan sebelum surat. Pembuat surat lama tidak ditebak. Queue sumber yang tidak kosong harus ditangani dahulu. Sesi/cache dimulai ulang dan pengguna login kembali. Database MySQL dibangun dengan migration, bukan menyalin tabel migrations SQLite.

Backup rutin MySQL: gunakan mysqldump --single-transaction --routines --triggers --no-tablespaces dengan kredensial dari option file lokal, lalu backup storage/app/public. Batasi penulisan lampiran selama mengambil pasangan backup database/storage agar konsisten. Simpan APP_KEY bersama konfigurasi pemulihan secara aman.

Pemulihan dilakukan ke database baru dahulu, cocokkan lampiran dan jumlah data, lalu ubah konfigurasi koneksi. Sebelum MySQL menerima data baru, SQLite dapat digunakan kembali dengan konfigurasi asli. Setelah terdapat data baru di MySQL, lakukan rekonsiliasi sebelum kembali ke sumber SQLite.

## Pembersihan lampiran

Lampiran lama dihapus setelah transaksi database berhasil. Jika cleanup gagal, manifest retry berada pada storage disk local, direktori attachment-cleanup.

```powershell
php artisan sipas:cleanup-attachments
```

Perintah dijadwalkan setiap jam. Pada server, aktifkan Laravel scheduler; pada localhost dapat menjalankan php artisan schedule:work atau cleanup manual. Kegagalan penyimpanan surat menghapus upload baru dan mempertahankan lampiran lama.
