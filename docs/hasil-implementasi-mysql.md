# Hasil implementasi SIPAS dan migrasi MySQL

Tanggal verifikasi: 17 September 2026. Status: implementasi inti selesai, aplikasi aktif di http://127.0.0.1:8000.

## Hasil yang diterapkan

Aplikasi Laravel menggunakan server **MySQL 8.4.10**, database operasional `sipas_db`. Server ini adalah MySQL, terpisah dari binary MariaDB yang dibundel XAMPP. PHP tetap menggunakan instalasi XAMPP. Jalankan ulang melalui `powershell -ExecutionPolicy Bypass -File .\Start-Localhost.ps1`; skrip memeriksa koneksi dan enam foreign key sebelum menjalankan aplikasi.

Skema berisi **11 tabel: 3 tabel utama dan 8 tabel pendukung Laravel**, dengan **6 foreign key**. Tabel utama tetap `users`, `surat_masuk`, dan `surat_keluar`; tidak ada tabel bisnis tambahan yang dibuat hanya untuk menambah jumlah relasi.

| Relasi | Pemakaian pada aplikasi | Aturan database |
| --- | --- | --- |
| surat_masuk.created_by → users.id | Identitas administrator yang mencatat surat | RESTRICT |
| surat_masuk.updated_by → users.id | Administrator yang terakhir mengubah surat | RESTRICT |
| surat_keluar.created_by → users.id | Identitas administrator yang mencatat surat | RESTRICT |
| surat_keluar.updated_by → users.id | Administrator yang terakhir mengubah surat | RESTRICT |
| surat_keluar.surat_masuk_id → surat_masuk.id | Referensi surat masuk yang dibalas, opsional | RESTRICT |
| sessions.user_id → users.id | Sesi pengguna terautentikasi; nullable untuk sesi tamu | CASCADE |

Semua hubungan juga didefinisikan melalui belongsTo/hasMany pada model Laravel. Form dan impor mengisi pencatat dari pengguna yang login, bukan menerima identitas pembuat dari input klien. Detail surat menampilkan pencatat, pengubah terakhir, dan tautan surat balasan dua arah. Pemilihan surat asal menggunakan pencarian terautentikasi dengan hasil terbatas.

Akun administrator dinonaktifkan menggunakan soft delete. Sesi dan token remember-me dicabut, login akun tersebut ditolak, sedangkan nama pencatat tetap dapat ditampilkan pada surat lama. Akun sendiri dan administrator aktif terakhir dilindungi. Penghapusan fisik pengguna yang masih direferensikan surat ditolak oleh database.

Penyimpanan surat dan konfirmasi impor menggunakan transaksi. Upload baru dibersihkan ketika transaksi gagal; lampiran lama dihapus setelah transaksi berhasil. Pembersihan yang gagal memiliki manifest retry dan perintah `sipas:cleanup-attachments`. Penghapusan/reset surat masuk ditolak jika masih memiliki surat balasan. Reset memerlukan konfirmasi dan tidak lagi menjalankan ALTER TABLE di dalam transaksi.

Impor Excel/CSV mendukung header datar dan format agenda hasil ekspor, memilih sheet surat yang sesuai, memvalidasi tanggal/referensi, melewati duplikat dan baris invalid, serta menggunakan token pratinjau sekali pakai yang terikat pengguna/sesi. Ekspor tetap memakai format agenda lama. Relasi balasan tidak tercantum dalam format ekspor lama; gunakan form atau template datar dengan surat_masuk_id untuk mengisinya.

CSS Tailwind dan Alpine dibangun melalui Vite, sehingga tidak bergantung pada CDN runtime untuk tampilan dan buka/tutup menu. Fitur OCR menggunakan aset Tesseract lokal yang sudah tersedia. Pemeriksaan browser mencakup layar desktop dan ponsel.

## Migrasi dan perlindungan data

Backup awal berada di `backups/20260916-212008`; backup terakhir sebelum perpindahan koneksi berada di `backups/cutover-20260916-214508`. Backup mencakup snapshot SQLite konsisten, konfigurasi lokal, dan storage/app. Direktori backup dan kredensial lokal diabaikan Git.

Inventaris saat migrasi: **1 pengguna, 0 surat masuk, dan 0 surat keluar**. Data pengguna berhasil dipindahkan; ID, password hash, timestamp, dan seluruh nilai sumber diperiksa. Database SQLite dan berkas asli dipertahankan untuk pemulihan. Pengguna login kembali karena sesi/cache sumber tidak dipindahkan. Tidak ada identitas pencatat surat lama yang ditebak.

Command `sipas:transfer-sqlite` menyediakan dry-run dan menolak database tujuan yang tabel utamanya sudah berisi data. Akun operasional, akun pengujian otomatis, dan akun pengujian browser dibatasi pada database masing-masing. Pengujian tidak menggunakan database operasional.

## Bukti verifikasi

| Pemeriksaan | Hasil |
| --- | --- |
| PHPUnit pada MySQL sipas_test | 46 test, 401 assertion, 0 failure, 0 error |
| Browser Chrome/Playwright pada sipas_ui_test | 8 kelompok skenario lulus |
| Error JavaScript / respons HTTP gagal dari aplikasi dan aset lokal saat pengujian browser | 0 / 0 |
| Vite production build | Berhasil |
| Pemeriksaan sintaks PHP dan format Laravel Pint | 63 berkas PHP lulus; format pengujian baru lulus |
| sipas:health pada database operasional | MySQL 8.4.10, sipas_db, 6 foreign key |
| Halaman login localhost | HTTP 200 |

Pengujian browser mencakup login dan sesi database, semua menu utama dan halaman impor, impor/konfirmasi satu workbook Sheet1 masuk dan Sheet2 keluar, pencatatan surat masuk beserta upload/lampiran, pencarian serta pemilihan surat balasan, tautan detail dua arah, buka/tutup menu ponsel tanpa overflow horizontal, unduhan Excel, dan pembacaan gambar melalui OCR. Data browser tersimpan pada database pengujian terpisah, bukan pada arsip operasional.

Tabel black box laporan memuat 54 skenario dengan input, hasil yang diharapkan, hasil aktual, dan status. Berkas tersedia dalam `docs/Tabel_Pengujian_BlackBox_SIPAS.docx` dan `docs/tabel-pengujian-blackbox.md`; pemetaan skenario terhadap bukti eksekusi berada dalam `docs/bukti-pengujian-blackbox.json`. Satu metode pengujian dapat memverifikasi beberapa skenario. Pemeriksaan internal seperti foreign key dan rollback dipisahkan sebagai pemeriksaan teknis pendukung.

Bukti lokal yang dapat diperiksa ulang: `.local/phpunit-results.xml`, `.local/browser-results.json`, `.local/browser-detail-desktop.png`, dan `.local/browser-detail-mobile.png`. Direktori .local sengaja tidak disertakan Git karena memuat runtime dan konfigurasi lokal.

## Matriks pengujian black box

| Skenario / input | Perilaku yang diharapkan dan telah diamati | Hasil |
| --- | --- | --- |
| Membuka halaman administrasi sebagai tamu | Dialihkan ke login | Lulus |
| Login benar dan logout | Sesi dibuat lalu akses dicabut | Lulus |
| Login akun dinonaktifkan | Autentikasi ditolak | Lulus |
| Membuka dashboard, profil, CRUD, dan impor | Halaman tampil tanpa error server/JavaScript | Lulus |
| Menambah dan mengubah surat | Pencatat diisi dari sesi; pengubahan tidak mengganti pencatat awal | Lulus |
| Mengirim created_by/updated_by palsu | Validasi menolak input | Lulus |
| Tanggal invalid/di luar rentang atau teks terlalu panjang | Pesan validasi, tanpa error tipe/ukuran MySQL | Lulus |
| Surat keluar biasa / surat balasan | Referensi boleh kosong; referensi valid tampil pada detail kedua surat | Lulus |
| Referensi balasan tidak tersedia | Penyimpanan ditolak | Lulus |
| Menghapus/reset surat masuk yang sudah dibalas | Ditolak; data dan hubungan dipertahankan | Lulus |
| Reset tanpa konfirmasi / dengan konfirmasi | Tanpa konfirmasi ditolak; reset sah hanya menghapus jenis surat yang dipilih | Lulus |
| Menonaktifkan admin lain / akun sendiri / admin terakhir | Admin lain nonaktif dan sesi dicabut; diri sendiri/admin terakhir dilindungi | Lulus |
| Mengubah profil/password dan menambah admin | Data tervalidasi dan tersimpan | Lulus |
| Pencarian, filter, dan lookup surat asal | Hasil sesuai kriteria; input filter invalid ditolak | Lulus |
| Impor agenda masuk/keluar hasil ekspor | Format terbaca; data duplikat dilewati | Lulus |
| CSV berisi data valid, invalid, duplikat dan ID balasan | Pratinjau/konfirmasi sesuai validasi; identitas klien tidak digunakan | Lulus |
| Workbook sama: Sheet1 masuk, Sheet2 keluar | Jenis sheet dipilih dari header; kedua impor dan konfirmasi menyimpan jenis surat yang sesuai | Lulus |
| Agenda Sheet2 dengan header multiline dan tanggal merge | Header dikenali; tanggal diteruskan ke baris merge yang sesuai | Lulus |
| Agenda keluar lama dengan kolom tanggal keluar kosong dan header tiga tingkat | Tanggal surat menjadi pengganti dengan catatan pratinjau; judul baris 6 dilewati | Lulus |
| Nama sheet salah / sheet keluar tidak tersedia / tanggal kosong | Isi header menentukan jenis; ketiadaan sheet/tanggal dilaporkan tanpa mencampur data surat masuk | Lulus |
| Mengulang token impor / memakai token pengguna lain | Ditolak dan tidak menambah data | Lulus |
| Kegagalan pada baris kedua saat konfirmasi impor | Seluruh baris di-rollback; pratinjau tetap dapat dicoba kembali | Lulus |
| Lampiran gagal ditulis ke storage | Pesan validasi; surat dan referensi lampiran tidak dibuat | Lulus |
| Mengganti, menghapus, atau gagal menyimpan lampiran | Cleanup sesuai hasil transaksi; file lama bertahan jika penyimpanan gagal | Lulus |
| Unduhan ekspor melalui browser | Berkas Excel terunduh dan tidak kosong | Lulus |
| OCR gambar uji | Teks surat berhasil dikenali | Lulus |
| Membuka dan menutup menu pada layar 390 px | Menu bekerja; detail tidak meluber secara horizontal | Lulus |
| Insert langsung FK pengguna/sesi/balasan yang tidak ada | MySQL menolak record yatim | Lulus |
| Hapus fisik pengguna yang direferensikan surat | MySQL menolak pelanggaran referensi | Lulus |
| Transfer SQLite dry-run, commit, lalu tujuan tidak kosong | Dry-run tidak menulis; transfer mempertahankan nilai; transfer ulang ditolak | Lulus |

## Dokumen dan diagram untuk laporan

Berkas asli pengguna "Surat Keluar Masuk Februari 2026.xlsx" juga diperiksa. Sheet Surat Keluar mempunyai 104 baris data mulai baris 7; seluruh tanggal keluar pada kolom B kosong. Pemulihan aturan agenda lama menghasilkan 104 valid, 0 duplikat, dan 0 invalid pada pratinjau. Aturan ini tidak menebak tanggal hari ini: tanggal surat pada berkas digunakan dan dijelaskan pada pratinjau. Tanggal keluar eksplisit yang invalid serta template datar dengan tanggal keluar kosong tetap tidak diterima.

- [ERD tabel utama — PNG](../database/ERD-MySQL-utama.png) dan [SVG](../database/ERD-MySQL-utama.svg).
- [ERD lengkap — PNG](../database/ERD-MySQL-lengkap.png) dan [SVG](../database/ERD-MySQL-lengkap.svg).
- [Definisi SQL aktual](../database/schema-mysql.sql) dan [metadata skema JSON](../database/schema-mysql.json).
- [Kamus data seluruh tabel](kamus-data-mysql.md).
- [Penjelasan gambar untuk 5.2.1 Implementasi Sistem](penjelasan-implementasi-5.2.1.md).
- [Panduan startup, pengujian, backup, dan pemulihan](panduan-mysql.md).

Gunakan diagram bernama ERD-MySQL sebagai bukti implementasi terbaru. Berkas ERD sebelumnya dipertahankan sebagai dokumentasi kondisi awal dan relasi logis sebelum perubahan.

## Ruang lingkup yang tetap berlaku

updated_by menyimpan pengubah terakhir, bukan seluruh log perubahan. Nomor agenda tetap berupa rekomendasi yang dapat diedit; tahap agenda_counters untuk penomoran atomik tidak diterapkan karena dalam rencana merupakan tahap opsional yang memerlukan keputusan aturan nomor. Arsip tetap dikelola bersama oleh administrator sesuai perilaku aplikasi semula. Kolom FK nullable mempertahankan data historis dan hubungan balasan opsional; pencatat surat baru tetap diwajibkan oleh proses aplikasi.

Hasil verifikasi berlaku untuk skenario dan lingkungan di atas. Penambahan fitur atau perubahan aturan bisnis berikutnya perlu menjalankan kembali pengujian yang relevan.
