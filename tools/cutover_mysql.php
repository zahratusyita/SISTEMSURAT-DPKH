<?php

$root = dirname(__DIR__);
$credentials = json_decode(file_get_contents($root.'/.local/mysql-credentials.json'), true, flags: JSON_THROW_ON_ERROR);
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=sipas_db;charset=utf8mb4', 'sipas_app', $credentials['app_password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
if (! $pdo->query('SELECT COUNT(*) FROM users WHERE deleted_at IS NULL')->fetchColumn()) {
    throw new RuntimeException('Transfer akun harus selesai sebelum koneksi diganti.');
}
$values = ['DB_CONNECTION' => 'mysql', 'DB_HOST' => '127.0.0.1', 'DB_PORT' => '3306', 'DB_DATABASE' => 'sipas_db', 'DB_USERNAME' => 'sipas_app', 'DB_PASSWORD' => $credentials['app_password'], 'DB_CHARSET' => 'utf8mb4', 'DB_COLLATION' => 'utf8mb4_unicode_ci'];
$environment = file_get_contents($root.'/.env');
foreach ($values as $key => $value) {
    $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
    if (preg_match($pattern, $environment)) {
        $environment = preg_replace_callback($pattern, fn () => $key.'='.$value, $environment);
    } else {
        $environment = rtrim($environment)."\n".$key.'='.$value."\n";
    }
}
file_put_contents($root.'/.env', $environment);
echo 'Koneksi aplikasi diubah ke MySQL sipas_db. APP_KEY asli dipertahankan.'.PHP_EOL;
