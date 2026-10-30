<?php

$root = dirname(__DIR__);
$credentials = json_decode(file_get_contents($root.'/.local/mysql-credentials.json'), true, flags: JSON_THROW_ON_ERROR);
foreach (['APP_ENV' => 'local', 'DB_CONNECTION' => 'mysql', 'DB_HOST' => '127.0.0.1', 'DB_PORT' => '3306', 'DB_DATABASE' => 'sipas_ui_test', 'DB_USERNAME' => 'sipas_ui_tester', 'DB_PASSWORD' => $credentials['ui_password'], 'SESSION_DRIVER' => 'database', 'CACHE_STORE' => 'database'] as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $_SERVER[$key] = $value;
}
