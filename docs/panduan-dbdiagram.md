# Membuka ERD SIPAS pada dbdiagram

## Penyebab pesan error

File backups/download/sipas_db_mysql.sql sudah diperiksa: terdapat tepat 11 CREATE TABLE dengan nama berbeda. Kolom created_by, updated_by, surat_masuk_id, dan deleted_at juga terdapat pada definisi tabel yang sesuai. File tersebut telah berhasil diimpor ke server MySQL sementara dan hasilnya diperiksa.

Pesan "Table ... already exists" dari editor dbdiagram menunjukkan bahwa dokumen yang diproses memiliki lebih dari satu definisi untuk nama tabel yang sama. Jika definisi lama belum memiliki kolom relasi lalu digabung dengan definisi terbaru, muncul pula pesan "Column ... does not exist". Berdasarkan pesan pengguna, ini merupakan kemungkinan penyebab; isi dokumen editor pengguna belum diperiksa langsung.

Namespace public pada pesan tersebut merupakan namespace diagram; aplikasi SIPAS tetap menggunakan database MySQL sipas_db. Pesan ini tidak menunjukkan bahwa database operasional telah berpindah ke PostgreSQL.

## Cara yang dianjurkan: file DBML

1. Simpan salinan diagram lama jika masih diperlukan, lalu buat diagram baru di dbdiagram.
2. Buka file **database/SIPAS-dbdiagram-lengkap.dbml** dan salin seluruh isinya.
3. Klik editor kode di sebelah kiri dbdiagram, tekan **Ctrl+A** untuk memilih seluruh kode bawaan/lama, lalu tempel isi file DBML sebagai pengganti.
4. Jangan menambahkan DBML ke bagian bawah definisi tabel lama. Jangan memilih importer MySQL untuk file .dbml: file tersebut sudah menggunakan bahasa editor dbdiagram.
5. Diagram lengkap akan menampilkan **11 tabel dan 6 hubungan foreign key**.

Untuk diagram bisnis yang lebih ringkas pada laporan, gunakan **database/SIPAS-dbdiagram-utama.dbml** sebagai pengganti seluruh isi editor. Diagram ini memuat **3 tabel utama dan 5 hubungan foreign key**; tabel sessions dan foreign key sesi hanya muncul pada diagram lengkap.

Kolom relasi pada kedua file sama dengan database aktual. Setiap surat memiliki referensi pencatat dan pengubah terakhir. Surat keluar juga memiliki referensi surat masuk asal balasan, yang bersifat opsional. Penghapusan akun menggunakan deleted_at (soft delete), tanpa menambahkan relasi palsu dari deleted_at.

## Alternatif: importer MySQL

Jika ingin memakai fitur Import SQL, buat diagram baru dan gunakan **database/SIPAS-dbdiagram-lengkap.sql** atau **database/SIPAS-dbdiagram-utama.sql**, lalu pilih format **MySQL**. File ini hanya berisi CREATE TABLE, indeks, dan constraint, tanpa data akun, perintah DROP TABLE, maupun perintah khusus dump.

File SQL diagram ditujukan untuk konversi struktur ke diagram. Untuk pemulihan database aplikasi beserta data, tetap gunakan **backups/download/sipas_db_mysql.sql**. Jangan menumpuk beberapa hasil impor pada dokumen yang sama.

## Verifikasi file

DBML dibuat dari database/schema-mysql.sql menggunakan paket resmi @dbml/core. Kedua file divalidasi memakai parser DBML lama dan parser DBML baru (dbmlv2). Jumlah tabel/relasi, daftar seluruh kolom, tipe data termasuk UNSIGNED, dan nullable dibandingkan dengan metadata database/schema-mysql.json. Operator relasi menggunakan sintaks klasik agar kompatibel; sifat opsional tetap ditentukan oleh kolom FK yang nullable. Tidak ada data akun, password hash, atau sesi yang dimasukkan ke file diagram.

Referensi resmi: [editor dbdiagram](https://docs.dbdiagram.io/basic-editing-experience/), [sintaks DBML](https://dbml.dbdiagram.io/docs/), dan [API @dbml/core](https://dbml.dbdiagram.io/js-module/core/).

Jika skema aplikasi berubah, ekspor ulang metadata terlebih dahulu lalu jalankan tools/export_dbdiagram.cjs. Pada komputer ini paket validator dipasang pada .local/dbml-validator, sehingga dependency produksi aplikasi dan package-lock.json proyek tidak diubah untuk konversi ERD.
