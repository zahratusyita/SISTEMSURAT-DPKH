<?php

// Provisioning helper: run Artisan against the local MySQL target before cutover.
$root = dirname(__DIR__);
$credentials = json_decode(file_get_contents($root.'/.local/mysql-credentials.json'), true, flags: JSON_THROW_ON_ERROR);
foreach (['DB_CONNECTION' => 'mysql', 'DB_HOST' => '127.0.0.1', 'DB_PORT' => '3306', 'DB_DATABASE' => 'sipas_db', 'DB_USERNAME' => 'sipas_app', 'DB_PASSWORD' => $credentials['app_password']] as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $_SERVER[$key] = $value;
}
chdir($root);
require $root.'/artisan';
