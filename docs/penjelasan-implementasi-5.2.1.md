# 5.2.1 Implementasi Sistem

Implementasi sistem merupakan tahap penerapan rancangan ke dalam aplikasi web Sistem Informasi Pengelolaan Administrasi Surat (SIPAS). Aplikasi dikembangkan menggunakan framework Laravel dan database MySQL untuk mendukung pengelolaan surat masuk, surat keluar, serta akun administrator. Hubungan antardata diterapkan melalui foreign key sehingga identitas pencatat surat dan hubungan surat balasan dapat disimpan secara konsisten. Berikut penjelasan tampilan pada masing-masing fitur yang dapat digunakan sebagai uraian gambar dalam laporan. Nomor gambar disesuaikan dengan urutan gambar pada dokumen laporan.

## a. Halaman Login

**Keterangan gambar: Tampilan halaman login.**

Halaman login merupakan halaman awal yang digunakan administrator untuk mengakses sistem. Pada halaman ini, pengguna memasukkan alamat email dan kata sandi yang telah terdaftar. Sistem memeriksa kesesuaian kredensial dengan data akun pada database. Apabila data yang dimasukkan benar dan akun masih aktif, pengguna diarahkan ke halaman dashboard. Apabila autentikasi gagal, sistem menampilkan pesan kesalahan agar pengguna dapat memperbaiki data yang dimasukkan. Opsi mengingat akun tersedia untuk mempertahankan autentikasi sesuai mekanisme aplikasi.

Setelah login berhasil, sistem menyimpan sesi pengguna yang terhubung dengan tabel users. Sesi tersebut digunakan untuk membatasi akses halaman administrasi sekaligus menentukan identitas administrator yang mencatat atau memperbarui surat. Pengguna dapat mengakhiri akses melalui fitur logout.

## b. Halaman Dashboard

**Keterangan gambar: Tampilan halaman dashboard.**

Halaman dashboard ditampilkan setelah administrator berhasil login. Halaman ini menyajikan ringkasan jumlah surat masuk dan surat keluar, baik pada bulan berjalan, tahun berjalan, maupun keseluruhan data. Grafik jumlah surat per bulan membantu administrator mengetahui perkembangan aktivitas surat selama satu tahun. Dashboard juga menampilkan daftar aktivitas surat terbaru sebagai sarana untuk melihat pencatatan yang baru dilakukan.

Informasi pada dashboard diperoleh dari data surat yang tersimpan dalam database. Menu navigasi menyediakan akses menuju pengelolaan surat masuk, surat keluar, serta profil akun sehingga administrator dapat berpindah ke fitur yang diperlukan.

## c. Halaman Surat Masuk

**Keterangan gambar: Tampilan halaman pengelolaan surat masuk.**

Halaman surat masuk digunakan untuk mencatat dan mengelola surat yang diterima oleh instansi. Informasi yang dikelola meliputi tanggal penerimaan, nomor agenda, alamat pengirim, tanggal surat, nomor surat, perihal, tujuan disposisi, dan lampiran. Administrator dapat menambahkan surat baru, melihat detail, memperbarui informasi, dan menghapus surat sesuai aturan sistem. Fitur pencarian serta filter bulan dan tahun membantu menemukan arsip yang diperlukan. Nomor agenda disediakan sebagai rekomendasi yang masih dapat disesuaikan oleh administrator.

Sistem menyediakan impor Excel/CSV dengan pratinjau sebelum penyimpanan dan ekspor Excel berdasarkan filter yang dipilih. Lampiran dapat disimpan dalam format yang didukung, sedangkan fitur OCR membantu mengenali teks dari dokumen yang diunggah. Informasi hasil pengenalan tetap perlu diperiksa oleh administrator sebelum digunakan.

Pada halaman detail, sistem menampilkan identitas pencatat, pengubah terakhir, dan daftar surat keluar yang menjadi balasan. Data pencatat dihubungkan ke tabel users, sedangkan daftar balasan berasal dari referensi surat_masuk_id pada tabel surat_keluar. Surat masuk yang masih memiliki balasan tidak dapat dihapus atau direset agar hubungan arsip tetap terjaga.

## d. Halaman Surat Keluar

**Keterangan gambar: Tampilan halaman pengelolaan surat keluar.**

Halaman surat keluar digunakan untuk mencatat surat yang dikirim oleh instansi. Informasi yang dicatat meliputi tanggal pengiriman, nomor agenda, alamat penerima, tanggal surat, nomor surat, perihal, asal surat, dan lampiran. Administrator dapat menambahkan, melihat, mengubah, serta menghapus data surat. Halaman ini juga menyediakan pencarian, filter periode, impor dengan pratinjau, ekspor Excel, dan bantuan OCR untuk mendukung pengelolaan arsip.

Apabila surat keluar merupakan balasan, administrator dapat mencari dan memilih surat masuk yang menjadi asal balasan pada formulir. Pilihan tersebut bersifat opsional karena tidak semua surat keluar dibuat sebagai balasan. Setelah disimpan, halaman detail menampilkan tautan menuju surat masuk asal sehingga keterkaitan kedua dokumen dapat ditelusuri. Sistem juga menyimpan identitas pencatat dan pengubah terakhir berdasarkan akun yang sedang login.

## e. Halaman Profil Akun

**Keterangan gambar: Tampilan halaman profil dan pengelolaan akun administrator.**

Halaman profil akun digunakan administrator untuk memperbarui nama dan alamat email serta mengganti kata sandi. Perubahan kata sandi memerlukan verifikasi kata sandi lama dan konfirmasi kata sandi baru. Sistem melakukan validasi sebelum menyimpan perubahan agar data akun memenuhi ketentuan aplikasi. Halaman ini juga menyediakan fasilitas penambahan akun administrator untuk mendukung penggunaan sistem oleh lebih dari satu petugas.

Administrator dapat menonaktifkan akun administrator lain melalui tindakan penghapusan akun, dengan perlindungan terhadap akun sendiri dan administrator aktif terakhir. Penonaktifan menggunakan soft delete sehingga data identitas tetap tersedia untuk riwayat pencatatan surat. Sesi akun yang dinonaktifkan dicabut dan akun tersebut tidak dapat login kembali. Dengan demikian, pengelolaan akun tetap mempertahankan keterkaitan data surat dengan administrator yang sebelumnya mencatatnya.

## Catatan penggunaan pada laporan

Gunakan tangkapan layar dari aplikasi setelah implementasi ini, terutama pada formulir surat keluar dan detail surat yang sudah menampilkan hubungan balasan serta identitas pencatat. Diagram yang sesuai dengan uraian ini adalah ERD-MySQL-utama atau ERD-MySQL-lengkap. Tabel utama berjumlah tiga; jumlah seluruh tabel termasuk pendukung Laravel adalah sebelas. Relasi tidak dibuat hanya pada gambar, tetapi juga diterapkan sebagai enam foreign key pada MySQL.
