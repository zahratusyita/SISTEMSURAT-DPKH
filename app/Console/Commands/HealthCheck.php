<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthCheck extends Command
{
    protected $signature = 'sipas:health';

    protected $description = 'Memeriksa server MySQL dan foreign key aplikasi';

    public function handle(): int
    {
        try {
            $version = DB::selectOne('SELECT VERSION() AS version')->version;
            if (DB::getDriverName() !== 'mysql' || str_contains(strtolower($version), 'mariadb')) {
                $this->error('Database aktif harus MySQL.');

                return self::FAILURE;
            }
            $count = DB::selectOne('SELECT COUNT(*) AS total FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE()')->total;
            if ((int) $count !== 6) {
                $this->error('Skema relasi belum lengkap. Jalankan migration.');

                return self::FAILURE;
            }
            $this->info('MySQL '.$version.' | database '.DB::connection()->getDatabaseName().' | '.$count.' foreign key');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Pemeriksaan database gagal. Periksa koneksi dan log lokal.');

            return self::FAILURE;
        }
    }
}
