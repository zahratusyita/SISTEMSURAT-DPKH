# Tabel pengujian black box SIPAS

Pengujian dilaksanakan pada 17 September 2026 terhadap SIPAS berbasis Laravel dan MySQL 8.4.10. Pengujian menggunakan database khusus yang terpisah dari arsip operasional. Metode black box menilai kesesuaian input, keluaran, pesan validasi, dan perilaku fitur melalui permintaan HTTP dan browser Chrome.

Seluruh 54 skenario dalam tabel memenuhi hasil yang diharapkan. Bukti eksekusi keseluruhan: 46 pengujian otomatis, 401 assertion, serta 8 kelompok pengujian browser lulus; tidak terdapat failure, error, atau permintaan aset lokal yang gagal pada eksekusi tersebut. Jumlah skenario tabel berbeda dari jumlah metode pengujian karena satu metode dapat memeriksa beberapa kondisi.

| No | Fitur dan skenario | Input atau tindakan | Hasil yang diharapkan | Hasil aktual | Status |
| --- | --- | --- | --- | --- | --- |
| 1 | Login dengan akun aktif | Email dan kata sandi benar. | Pengguna masuk ke dashboard. | Autentikasi berhasil dan dashboard dapat diakses. | Lulus |
| 2 | Login dengan kata sandi salah | Email terdaftar dan kata sandi salah. | Login ditolak dan pesan kesalahan ditampilkan. | Pesan validasi muncul; pengguna tetap belum login. | Lulus |
| 3 | Validasi formulir login | Kredensial kosong atau format email salah. | Kolom wajib dan format email divalidasi. | Input ditolak dengan pesan kesalahan pada kolom terkait. | Lulus |
| 4 | Pembatasan akses tamu | Buka halaman administrasi tanpa login. | Pengguna diarahkan ke halaman login. | Akses administrasi dialihkan ke login. | Lulus |
| 5 | Logout | Keluar dari akun yang sedang login. | Sesi autentikasi berakhir. | Pengguna keluar dan kembali ke halaman login. | Lulus |
| 6 | Login akun nonaktif | Gunakan kredensial akun yang telah dihapus. | Akun tidak dapat login. | Autentikasi akun nonaktif ditolak. | Lulus |
| 7 | Perhitungan dashboard | Fixture September 2026: 2 surat masuk bulan ini, 1 surat keluar bulan ini, 3 surat tahun ini, 5 surat keseluruhan. | Empat kartu menampilkan 2, 1, 3, dan 5. | Keempat angka sesuai; daftar surat terbaru tampil. | Lulus |
| 8 | Navigasi menu utama | Buka dashboard, surat masuk, surat keluar, profil, dan halaman impor. | Halaman tersedia dan dapat ditampilkan. | Seluruh halaman yang diperiksa berhasil dimuat. | Lulus |
| 9 | Tambah surat masuk | Isi data surat masuk dengan tanggal dan perihal valid. | Surat tersimpan dan tampil pada daftar. | Surat baru tampil pada daftar dan memiliki halaman detail. | Lulus |
| 10 | Detail surat masuk | Buka detail surat masuk yang baru dibuat. | Data surat dan pencatat ditampilkan. | Isi surat serta nama pencatat tampil sesuai data uji. | Lulus |
| 11 | Ubah surat masuk | Ubah perihal surat masuk yang tersimpan. | Perubahan ditampilkan setelah disimpan. | Detail menampilkan perihal yang telah diubah. | Lulus |
| 12 | Hapus surat masuk | Hapus surat masuk yang tidak memiliki balasan. | Surat hilang dari daftar dan detail tidak tersedia. | Surat tidak tampil; permintaan detail menghasilkan 404. | Lulus |
| 13 | Tambah surat keluar | Isi surat keluar biasa dengan data valid. | Surat tersimpan dan tampil pada daftar. | Surat baru tampil pada daftar dan memiliki detail. | Lulus |
| 14 | Detail surat keluar | Buka detail surat keluar yang baru dibuat. | Data surat dan pencatat ditampilkan. | Data surat dan nama pencatat tampil sesuai input. | Lulus |
| 15 | Ubah surat keluar | Ubah perihal surat keluar yang tersimpan. | Perubahan tersimpan dan ditampilkan. | Detail menampilkan perihal yang telah diubah. | Lulus |
| 16 | Hapus surat keluar | Hapus surat keluar yang tersimpan. | Surat hilang dari daftar dan detail tidak tersedia. | Surat tidak tampil; detail menghasilkan 404. | Lulus |
| 17 | Validasi kolom wajib surat | Kosongkan perihal, tanggal surat, serta tanggal masuk atau keluar. | Penyimpanan ditolak dengan pesan kolom wajib. | Tiga kolom tersebut menghasilkan kesalahan validasi pada kedua jenis surat. | Lulus |
| 18 | Validasi batas tanggal dan teks | Tanggal 0999-01-01, tanggal keluar dengan waktu, atau perihal 16.001 karakter. | Input di luar aturan penyimpanan ditolak. | Kesalahan validasi diterima; input tidak disimpan. | Lulus |
| 19 | Unggah dan buka lampiran | Unggah gambar PNG pada surat masuk lalu buka lampiran. | Lampiran tersimpan dan dapat dibuka. | Pencatatan berhasil; lampiran memberikan respons HTTP 200. | Lulus |
| 20 | Penolakan jenis lampiran | Unggah berkas EXE pada kedua jenis surat. | Jenis berkas yang tidak diizinkan ditolak. | Pesan validasi lampiran muncul pada kedua formulir. | Lulus |
| 21 | Batas ukuran lampiran | Unggah PDF berukuran 10.241 KB, melebihi batas 10 MB. | Berkas terlalu besar ditolak. | Pesan validasi ukuran lampiran muncul. | Lulus |
| 22 | Pembacaan OCR | Jalankan OCR pada gambar uji yang memuat tulisan SURAT. | Tulisan pada gambar dapat dikenali. | OCR menghasilkan teks yang memuat SURAT. | Lulus |
| 23 | Pencarian surat | Cari penanda BB-FEB pada kedua daftar surat. | Hanya data yang cocok ditampilkan. | Surat Februari tampil; data Januari dan Maret tidak tampil. | Lulus |
| 24 | Pencarian tanpa hasil | Cari teks yang tidak ada pada data uji. | Halaman menampilkan daftar kosong tanpa gagal. | Respons HTTP 200 dengan hasil kosong. | Lulus |
| 25 | Filter bulan dan tahun | Pilih Februari dan tahun 2026 pada kedua daftar. | Hanya surat Februari 2026 ditampilkan. | Data Februari tampil; data bulan lain tidak tampil. | Lulus |
| 26 | Urutan semua bulan | Tampilkan semua bulan untuk data Januari, Februari, dan Maret 2026. | Surat diurutkan berdasarkan tanggal surat masuk atau keluar dari terbaru. | Urutan Maret, Februari, Januari sesuai pada kedua daftar. | Lulus |
| 27 | Validasi parameter filter | Gunakan bulan 99 atau tahun dua digit 20. | Parameter tidak valid ditolak. | Permintaan menghasilkan validasi parameter filter. | Lulus |
| 28 | Pembagian halaman | Tampilkan 12 surat lalu buka halaman kedua. | Halaman pertama berisi 10 surat; halaman kedua 2 surat. | Halaman pertama memuat surat 12–03; halaman kedua 02–01. | Lulus |
| 29 | Hubungan surat balasan | Pilih surat masuk sebagai asal balasan surat keluar. | Referensi tersimpan; detail memiliki tautan dua arah. | Pencarian, pemilihan, serta tautan detail surat asal dan balasan berfungsi. | Lulus |
| 30 | Referensi balasan opsional | Buat surat keluar biasa; hapus referensi balasan saat mengubah surat. | Surat keluar dapat disimpan tanpa surat asal. | Pembuatan biasa dan pengosongan referensi berhasil. | Lulus |
| 31 | Referensi balasan tidak valid | Isi surat_masuk_id dengan ID 999999 yang tidak tersedia. | Penyimpanan ditolak. | Pesan validasi referensi surat masuk diterima. | Lulus |
| 32 | Perlindungan surat asal | Hapus atau reset surat masuk yang masih memiliki surat balasan. | Tindakan ditolak untuk menjaga hubungan surat. | Penghapusan dan reset ditolak; surat asal tetap tersedia. | Lulus |
| 33 | Reset tanpa konfirmasi | Reset surat keluar tanpa konfirmasi yang diwajibkan. | Reset ditolak. | Pesan validasi muncul dan data tetap ada. | Lulus |
| 34 | Reset dengan konfirmasi | Reset surat keluar menggunakan konfirmasi HAPUS. | Surat keluar dihapus; surat masuk tetap ada. | Daftar surat keluar kosong; data surat masuk tetap tersedia. | Lulus |
| 35 | Pratinjau impor CSV | Unggah CSV dengan 1 baris valid, 1 duplikat, dan 1 invalid. | Pratinjau membedakan ketiga status. | Jumlah pratinjau sesuai: 1 valid, 1 duplikat, 1 invalid. | Lulus |
| 36 | Konfirmasi impor valid | Simpan hasil pratinjau CSV tersebut. | Hanya baris valid ditambahkan. | Satu surat baru disimpan; duplikat dan baris invalid dilewati. | Lulus |
| 37 | Perlindungan konfirmasi impor | Gunakan ulang token impor atau konfirmasi dengan pengguna lain. | Konfirmasi yang tidak sah ditolak. | Token ulang dan token milik pengguna lain ditolak. | Lulus |
| 38 | Pemilihan Sheet1 dan Sheet2 | Impor satu workbook: Sheet1 surat masuk dan Sheet2 surat keluar. | Masing-masing fitur membaca sheet yang sesuai. | Pratinjau dan konfirmasi kedua jenis surat berhasil memakai sheet yang sesuai. | Lulus |
| 39 | Impor agenda dengan header bertingkat | Gunakan agenda dengan header multiline dan tanggal pada sel gabungan. | Header dan tanggal agenda dikenali. | Data surat keluar valid dikenali dengan tanggal yang sesuai. | Lulus |
| 40 | Tanggal keluar kosong pada agenda lama | Kosongkan kolom tanggal keluar; isi tanggal surat pada agenda dengan tiga baris header. | Tanggal surat menjadi tanggal keluar sesuai aturan agenda lama; baris judul dilewati. | Dua data uji valid terbaca; tanggal terisi dan catatan penyesuaian tersedia. | Lulus |
| 41 | Tanggal impor yang tidak valid | Isi tanggal agenda tidak valid, kosongkan kedua tanggal, atau kosongkan tanggal keluar pada template datar. | Input tanggal tidak valid tetap ditolak. | Ketiga kondisi menghasilkan baris invalid; tanggal salah tidak diganti otomatis. | Lulus |
| 42 | Pemilihan sheet berdasarkan isi | Nama sheet tidak sesuai header; uji pula nama sheet yang cocok dengan huruf berbeda. | Isi header menentukan jenis; sheet bernama sesuai diprioritaskan bila isinya cocok. | Sheet yang tepat dipilih pada kedua kondisi. | Lulus |
| 43 | Workbook tanpa sheet surat keluar | Impor melalui fitur surat keluar dengan workbook yang hanya berisi surat masuk. | Pesan bahwa sheet sesuai tidak tersedia ditampilkan. | Impor ditolak; baris surat masuk tidak dibaca sebagai surat keluar. | Lulus |
| 44 | Format impor tidak sesuai | Unggah CSV dengan header not,a,valid,template. | Format tidak sesuai ditolak. | Pesan kesalahan format impor ditampilkan. | Lulus |
| 45 | Ekspor surat ke Excel | Ekspor daftar surat masuk dan surat keluar. | Berkas Excel dapat diunduh. | Kedua endpoint memberi HTTP 200 dan berkas unduhan; unduhan browser berhasil. | Lulus |
| 46 | Perubahan profil akun | Ubah nama dan email dengan nilai valid. | Profil baru tersimpan. | Nama dan email diperbarui sesuai input. | Lulus |
| 47 | Validasi email profil dan admin | Gunakan email milik akun lain atau email berformat salah. | Email tidak valid atau duplikat ditolak. | Pesan validasi muncul; data profil lama tetap tersedia. | Lulus |
| 48 | Perubahan kata sandi valid | Isi kata sandi lama benar, kata sandi baru, dan konfirmasi yang cocok. | Kata sandi baru tersimpan. | Kata sandi akun berhasil diperbarui dan cocok dengan nilai baru. | Lulus |
| 49 | Penolakan perubahan kata sandi | Gunakan kata sandi lama salah, kata sandi baru kurang dari 8 karakter, atau konfirmasi berbeda. | Perubahan ditolak dengan pesan terkait. | Ketiga kondisi menghasilkan kesalahan validasi. | Lulus |
| 50 | Penambahan administrator | Isi nama, email unik, dan kata sandi valid. | Akun administrator baru tersedia. | Akun baru berhasil dibuat sesuai input. | Lulus |
| 51 | Penghapusan akun lain | Hapus administrator lain yang memiliki riwayat pencatatan surat. | Akun dinonaktifkan dan sesinya dicabut; riwayat pencatat terjaga. | Akun tidak dapat login; sesi dicabut dan identitas pencatat tetap ada. | Lulus |
| 52 | Perlindungan akun sendiri | Administrator mencoba menghapus akunnya sendiri. | Penghapusan ditolak. | Akun aktif sendiri tetap tersedia. | Lulus |
| 53 | Perlindungan identitas pencatat | Kirim created_by atau updated_by palsu saat menyimpan surat. | Input identitas palsu ditolak dan surat tidak disimpan. | Kesalahan validasi identitas muncul; tidak ada surat baru tersimpan. | Lulus |
| 54 | Tampilan dan menu ponsel | Buka detail balasan pada layar lebar 390 px lalu buka dan tutup menu. | Menu dapat digunakan tanpa halaman meluber horizontal. | Menu terbuka dan tertutup; tidak ada overflow horizontal. | Lulus |

Status Lulus berarti hasil aktual sesuai hasil yang diharapkan, termasuk ketika sistem menolak input yang salah. Pengujian ini menilai fungsi pada lingkungan localhost dan tidak mencakup uji beban atau pengujian penetrasi. Hasil ini tidak menjamin seluruh kemungkinan kondisi bebas kesalahan.

## Pemeriksaan teknis pendukung

Pemeriksaan teknis pendukung yang terpisah dari penilaian black box juga lulus: batasan foreign key MySQL, perlindungan penghapusan fisik pengguna yang direferensikan, rollback impor, penanganan kegagalan dan penggantian lampiran, serta pemindahan data SQLite. Build produksi Vite berhasil. Pemetaan setiap skenario terhadap bukti pengujian tersedia dalam bukti-pengujian-blackbox.json.
