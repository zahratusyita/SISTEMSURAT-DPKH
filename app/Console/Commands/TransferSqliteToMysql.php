<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PDO;
use RuntimeException;
use Throwable;

class TransferSqliteToMysql extends Command
{
    protected $signature = 'sipas:transfer-sqlite {--source= : Path database SQLite sumber} {--dry-run : Hanya memeriksa kelayakan transfer}';

    protected $description = 'Memindahkan data utama SQLite ke MySQL kosong dengan mempertahankan ID dan memverifikasi seluruh nilai';

    public function handle(): int
    {
        try {
            if (DB::connection()->getDriverName() !== 'mysql' || str_contains(strtolower(DB::selectOne('SELECT VERSION() AS version')->version), 'mariadb')) {
                throw new RuntimeException('Tujuan harus merupakan server MySQL.');
            }
            $path = realpath($this->option('source') ?: database_path('database.sqlite'));
            if (! $path || ! is_file($path)) {
                throw new RuntimeException('Database SQLite sumber tidak ditemukan.');
            }
            $source = new PDO('sqlite:'.$path, options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $source->exec('PRAGMA query_only=ON');
            foreach (['jobs', 'job_batches', 'failed_jobs'] as $table) {
                if ((int) $source->query('SELECT COUNT(*) FROM '.$table)->fetchColumn() !== 0) {
                    throw new RuntimeException('Selesaikan atau pindahkan queue '.$table.' sebelum transfer.');
                }
            }
            $tables = ['users', 'surat_masuk', 'surat_keluar'];
            $rows = [];
            foreach ($tables as $table) {
                if (DB::table($table)->exists()) {
                    throw new RuntimeException('Tabel tujuan '.$table.' harus kosong. Transfer tidak menimpa data.');
                }
                $rows[$table] = $source->query('SELECT * FROM '.$table.' ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
                $columns = array_column($source->query('PRAGMA table_info('.$table.')')->fetchAll(PDO::FETCH_ASSOC), 'name');
                if (array_diff($columns, Schema::getColumnListing($table))) {
                    throw new RuntimeException('Skema tujuan tidak dapat menampung seluruh kolom '.$table.'.');
                }
                if ($table !== 'users') {
                    foreach ($rows[$table] as $row) {
                        if (! empty($row['lampiran_path']) && ! Storage::disk('public')->exists($row['lampiran_path'])) {
                            throw new RuntimeException('Lampiran tidak ditemukan pada '.$table.' ID '.$row['id'].'. Pulihkan storage dahulu.');
                        }
                    }
                }
                $this->line($table.': '.count($rows[$table]).' baris');
            }
            if ($this->option('dry-run')) {
                $this->info('Dry run lulus. Tidak ada data yang diubah.');

                return self::SUCCESS;
            }
            DB::transaction(function () use ($tables, $rows) {
                foreach ($tables as $table) {
                    foreach (array_chunk($rows[$table], 100) as $chunk) {
                        if ($chunk !== []) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                    if (DB::table($table)->count() !== count($rows[$table])) {
                        throw new RuntimeException('Jumlah data berbeda setelah transfer '.$table.'.');
                    }
                    foreach ($rows[$table] as $row) {
                        $saved = (array) DB::table($table)->where('id', $row['id'])->first();
                        foreach ($row as $column => $value) {
                            if (($saved[$column] === null) !== ($value === null) || (string) $saved[$column] !== (string) $value) {
                                throw new RuntimeException('Validasi nilai gagal pada '.$table.' ID '.$row['id'].' kolom '.$column.'.');
                            }
                        }
                    }
                }
            });
            $this->info('Transfer selesai. ID, nilai data, password hash, dan referensi lampiran tervalidasi.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            // Never echo a SQL exception that could contain a password hash or credentials.
            report($exception);
            $this->error($exception instanceof RuntimeException && ! $exception instanceof QueryException ? $exception->getMessage() : 'Transfer gagal. Tidak ada data yang dikomit; periksa log lokal.');

            return self::FAILURE;
        }
    }
}
