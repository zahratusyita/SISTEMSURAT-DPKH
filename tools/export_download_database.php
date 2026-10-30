<?php

$root = dirname(__DIR__);
$credentials = json_decode(file_get_contents($root.'/.local/mysql-credentials.json'), true, flags: JSON_THROW_ON_ERROR);
$directory = $root.'/backups/download';
if (! is_dir($directory)) {
    mkdir($directory, 0700, true);
}
$optionFile = tempnam($root.'/.local', 'mysql-export-');
$schemaFile = tempnam($root.'/.local', 'mysql-schema-');
$dataFile = tempnam($root.'/.local', 'mysql-data-');
$binary = $root.'/.local/mysql-8.4.10-winx64/bin/mysqldump.exe';
$common = ['--defaults-extra-file='.$optionFile, '--host=127.0.0.1', '--port=3306', '--single-transaction', '--no-tablespaces', '--set-gtid-purged=OFF', '--column-statistics=0', '--default-character-set=utf8mb4', '--skip-comments'];
$run = function (array $arguments, string $output) use ($binary): void {
    $process = proc_open(array_merge([$binary], $arguments), [0 => ['pipe', 'r'], 1 => ['file', $output, 'w'], 2 => ['pipe', 'w']], $pipes);
    if (! is_resource($process)) {
        throw new RuntimeException('Program ekspor tidak dapat dijalankan.');
    }
    fclose($pipes[0]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    if (proc_close($process) !== 0) {
        throw new RuntimeException('Ekspor MySQL gagal: '.$error);
    }
};
try {
    file_put_contents($optionFile, "[client]\nuser=sipas_app\npassword=\"".$credentials['app_password']."\"\n");
    $run(array_merge($common, ['--no-data', 'sipas_db']), $schemaFile);
    // Preserve business records and migration status; runtime sessions/cache/queues start empty.
    $run(array_merge($common, ['--no-create-info', '--complete-insert', '--skip-add-locks', 'sipas_db', 'users', 'surat_masuk', 'surat_keluar', 'migrations']), $dataFile);
    $header = "-- SIPAS: database MySQL siap impor\n-- Dibuat: ".date('c')."\n-- Struktur seluruh 11 tabel, 6 foreign key, data utama dan migration.\n-- Sesi, cache, dan antrean tidak disertakan; berkas lampiran disimpan terpisah.\n-- Pilih database tujuan sebelum mengimpor. Tabel dengan nama sama akan diganti.\n\n";
    $path = $directory.'/sipas_db_mysql.sql';
    file_put_contents($path, $header.file_get_contents($schemaFile)."\n".file_get_contents($dataFile));
    echo 'File SQL dibuat: backups/download/sipas_db_mysql.sql ('.filesize($path).' byte)'.PHP_EOL;
} finally {
    foreach ([$optionFile, $schemaFile, $dataFile] as $temporary) {
        if (is_file($temporary)) {
            unlink($temporary);
        }
    }
}
