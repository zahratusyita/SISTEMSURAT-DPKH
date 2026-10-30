# Kamus data MySQL SIPAS

Sumber: skema aktual `sipas_db`, MySQL 8.4.10, 16 September 2026. Dokumen ini dibuat dari database/schema-mysql.json, bukan perkiraan model aplikasi.

Terdapat 3 tabel utama (users, surat_masuk, surat_keluar) dan 8 tabel pendukung Laravel. PK = primary key; UK = unique key; MUL = bagian indeks nonunik. FK dicantumkan secara terpisah karena kolom FK juga dapat memiliki indeks MUL.

## Relasi dan aturan penghapusan

| Kolom anak | Referensi | ON DELETE |
| --- | --- | --- |
| sessions.user_id | users.id | CASCADE |
| surat_keluar.created_by | users.id | RESTRICT |
| surat_keluar.surat_masuk_id | surat_masuk.id | RESTRICT |
| surat_keluar.updated_by | users.id | RESTRICT |
| surat_masuk.created_by | users.id | RESTRICT |
| surat_masuk.updated_by | users.id | RESTRICT |

Seluruh kolom FK nullable. Hubungan setiap record anak ke induk adalah 0 atau 1; setiap induk dapat mempunyai 0 sampai banyak record anak. Surat baru wajib memiliki pencatat yang diisi oleh aplikasi; nullable mempertahankan data lama yang pencatatnya tidak diketahui. updated_by baru terisi setelah perubahan dan surat_masuk_id hanya digunakan untuk surat balasan. sessions.user_id dapat kosong untuk sesi tamu.

Penghapusan akun melalui aplikasi adalah soft delete (deleted_at), sehingga referensi surat tetap sah. Penghapusan fisik akun yang direferensikan surat ditolak oleh RESTRICT. CASCADE pada sessions berlaku untuk penghapusan fisik; aplikasi juga mencabut sesi secara eksplisit ketika menonaktifkan akun.

## users

Akun administrator, autentikasi, dan identitas pencatat surat.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | Tidak | PK | — |
| name | varchar(255) | Tidak | — | — |
| email | varchar(255) | Tidak | UK | — |
| email_verified_at | timestamp | Ya | — | — |
| password | varchar(255) | Tidak | — | — |
| remember_token | varchar(100) | Ya | — | — |
| created_at | timestamp | Ya | — | — |
| updated_at | timestamp | Ya | — | — |
| deleted_at | timestamp | Ya | — | — |

## surat_masuk

Arsip surat diterima, lampiran, pencatat, serta pengubah terakhir.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | Tidak | PK | — |
| tanggal_masuk_surat | date | Tidak | MUL | — |
| nomor_urut | varchar(255) | Ya | — | — |
| alamat_pengirim | text | Ya | — | — |
| tanggal_surat | date | Tidak | — | — |
| nomor_surat | varchar(255) | Ya | MUL | — |
| perihal | text | Tidak | — | — |
| tujuan_disposisi | varchar(255) | Ya | — | — |
| lampiran_path | varchar(255) | Ya | — | — |
| lampiran_nama_asli | varchar(255) | Ya | — | — |
| lampiran_mime | varchar(255) | Ya | — | — |
| lampiran_ukuran | bigint unsigned | Ya | — | — |
| created_at | timestamp | Ya | — | — |
| updated_at | timestamp | Ya | — | — |
| created_by | bigint unsigned | Ya | MUL | users.id |
| updated_by | bigint unsigned | Ya | MUL | users.id |

## surat_keluar

Arsip surat dikirim, lampiran, pencatat, dan referensi balasan opsional.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | Tidak | PK | — |
| tanggal_keluar_surat | date | Tidak | MUL | — |
| nomor_urut | varchar(255) | Ya | — | — |
| alamat_penerima | text | Ya | — | — |
| tanggal_surat | date | Tidak | — | — |
| nomor_surat | varchar(255) | Ya | MUL | — |
| perihal | text | Tidak | — | — |
| asal | varchar(255) | Ya | — | — |
| lampiran_path | varchar(255) | Ya | — | — |
| lampiran_nama_asli | varchar(255) | Ya | — | — |
| lampiran_mime | varchar(255) | Ya | — | — |
| lampiran_ukuran | bigint unsigned | Ya | — | — |
| created_at | timestamp | Ya | — | — |
| updated_at | timestamp | Ya | — | — |
| created_by | bigint unsigned | Ya | MUL | users.id |
| updated_by | bigint unsigned | Ya | MUL | users.id |
| surat_masuk_id | bigint unsigned | Ya | MUL | surat_masuk.id |

## cache

Nilai cache Laravel.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| key | varchar(255) | Tidak | PK | — |
| value | mediumtext | Tidak | — | — |
| expiration | int | Tidak | — | — |

## cache_locks

Kunci untuk penguncian cache/proses.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| key | varchar(255) | Tidak | PK | — |
| owner | varchar(255) | Tidak | — | — |
| expiration | int | Tidak | — | — |

## failed_jobs

Catatan pekerjaan antrean yang gagal.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | Tidak | PK | — |
| uuid | varchar(255) | Tidak | UK | — |
| connection | text | Tidak | — | — |
| queue | text | Tidak | — | — |
| payload | longtext | Tidak | — | — |
| exception | longtext | Tidak | — | — |
| failed_at | timestamp | Tidak | — | — |

## job_batches

Metadata kumpulan pekerjaan antrean.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | varchar(255) | Tidak | PK | — |
| name | varchar(255) | Tidak | — | — |
| total_jobs | int | Tidak | — | — |
| pending_jobs | int | Tidak | — | — |
| failed_jobs | int | Tidak | — | — |
| failed_job_ids | longtext | Tidak | — | — |
| options | mediumtext | Ya | — | — |
| cancelled_at | int | Ya | — | — |
| created_at | int | Tidak | — | — |
| finished_at | int | Ya | — | — |

## jobs

Antrean pekerjaan Laravel.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | Tidak | PK | — |
| queue | varchar(255) | Tidak | MUL | — |
| payload | longtext | Tidak | — | — |
| attempts | tinyint unsigned | Tidak | — | — |
| reserved_at | int unsigned | Ya | — | — |
| available_at | int unsigned | Tidak | — | — |
| created_at | int unsigned | Tidak | — | — |

## migrations

Daftar migration yang telah diterapkan.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | int unsigned | Tidak | PK | — |
| migration | varchar(255) | Tidak | — | — |
| batch | int | Tidak | — | — |

## password_reset_tokens

Tabel bawaan token pemulihan; alur reset melalui email belum tersedia di aplikasi.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| email | varchar(255) | Tidak | PK | — |
| token | varchar(255) | Tidak | — | — |
| created_at | timestamp | Ya | — | — |

## sessions

Penyimpanan sesi login dan sesi tamu Laravel.

| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |
| --- | --- | --- | --- | --- |
| id | varchar(255) | Tidak | PK | — |
| user_id | bigint unsigned | Ya | MUL | users.id |
| ip_address | varchar(45) | Ya | — | — |
| user_agent | text | Ya | — | — |
| payload | longtext | Tidak | — | — |
| last_activity | int | Tidak | MUL | — |

Informasi indeks lengkap, default, charset, dan definisi constraint dapat diperiksa pada [schema-mysql.sql](../database/schema-mysql.sql). Diagram sebelum migrasi dengan nama ERD-relasi-logis/ERD-sistem-surat merupakan dokumentasi kondisi lama; gunakan ERD-MySQL untuk laporan implementasi terbaru.
