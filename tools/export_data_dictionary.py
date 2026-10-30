"""Generate the database dictionary from the inspected MySQL schema."""
import json
from pathlib import Path

root = Path(__file__).resolve().parent.parent
schema = json.loads((root / "database/schema-mysql.json").read_text(encoding="utf-8"))
relations = schema["relations"]
lines = [
    "# Kamus data MySQL SIPAS",
    "",
    f"Sumber: skema aktual `{schema['database']}`, MySQL {schema['version']}, 16 September 2026. "
    "Dokumen ini dibuat dari database/schema-mysql.json, bukan perkiraan model aplikasi.",
    "",
    "Terdapat 3 tabel utama (users, surat_masuk, surat_keluar) dan 8 tabel pendukung Laravel. "
    "PK = primary key; UK = unique key; MUL = bagian indeks nonunik. "
    "FK dicantumkan secara terpisah karena kolom FK juga dapat memiliki indeks MUL.",
    "",
    "## Relasi dan aturan penghapusan",
    "",
    "| Kolom anak | Referensi | ON DELETE |",
    "| --- | --- | --- |",
]
for relation in relations:
    lines.append(
        f"| {relation['child_table']}.{relation['child_column']} | "
        f"{relation['parent_table']}.{relation['parent_column']} | {relation['delete_rule']} |"
    )
lines += [
    "",
    "Seluruh kolom FK nullable. Hubungan setiap record anak ke induk adalah 0 atau 1; "
    "setiap induk dapat mempunyai 0 sampai banyak record anak. Surat baru wajib memiliki pencatat "
    "yang diisi oleh aplikasi; nullable mempertahankan data lama yang pencatatnya tidak diketahui. "
    "updated_by baru terisi setelah perubahan dan surat_masuk_id hanya digunakan untuk surat balasan. "
    "sessions.user_id dapat kosong untuk sesi tamu.",
    "",
    "Penghapusan akun melalui aplikasi adalah soft delete (deleted_at), sehingga referensi surat "
    "tetap sah. Penghapusan fisik akun yang direferensikan surat ditolak oleh RESTRICT. "
    "CASCADE pada sessions berlaku untuk penghapusan fisik; aplikasi juga mencabut sesi secara "
    "eksplisit ketika menonaktifkan akun.",
]
purposes = {
    "users": "Akun administrator, autentikasi, dan identitas pencatat surat.",
    "surat_masuk": "Arsip surat diterima, lampiran, pencatat, serta pengubah terakhir.",
    "surat_keluar": "Arsip surat dikirim, lampiran, pencatat, dan referensi balasan opsional.",
    "sessions": "Penyimpanan sesi login dan sesi tamu Laravel.",
    "password_reset_tokens": "Tabel bawaan token pemulihan; alur reset melalui email belum tersedia di aplikasi.",
    "migrations": "Daftar migration yang telah diterapkan.",
    "cache": "Nilai cache Laravel.",
    "cache_locks": "Kunci untuk penguncian cache/proses.",
    "jobs": "Antrean pekerjaan Laravel.",
    "job_batches": "Metadata kumpulan pekerjaan antrean.",
    "failed_jobs": "Catatan pekerjaan antrean yang gagal.",
}
order = ["users", "surat_masuk", "surat_keluar"] + [
    name for name in schema["tables"] if name not in {"users", "surat_masuk", "surat_keluar"}
]
for name in order:
    lines += ["", f"## {name}", "", purposes[name], "", "| Kolom | Tipe MySQL | Nullable | Indeks | Referensi FK |", "| --- | --- | --- | --- | --- |"]
    for column in schema["tables"][name]:
        reference = next((
            f"{r['parent_table']}.{r['parent_column']}"
            for r in relations if r["child_table"] == name and r["child_column"] == column["name"]
        ), "—")
        key = {"PRI": "PK", "UNI": "UK", "MUL": "MUL", "": "—"}[column["column_key"]]
        lines.append(f"| {column['name']} | {column['type']} | {'Ya' if column['nullable'] == 'YES' else 'Tidak'} | {key} | {reference} |")
lines += [
    "",
    "Informasi indeks lengkap, default, charset, dan definisi constraint dapat diperiksa pada "
    "[schema-mysql.sql](../database/schema-mysql.sql). Diagram sebelum migrasi dengan nama "
    "ERD-relasi-logis/ERD-sistem-surat merupakan dokumentasi kondisi lama; gunakan ERD-MySQL untuk laporan implementasi terbaru.",
    "",
]
(root / "docs/kamus-data-mysql.md").write_text("\n".join(lines), encoding="utf-8")
print("Kamus data: 11 tabel, 6 foreign key.")
