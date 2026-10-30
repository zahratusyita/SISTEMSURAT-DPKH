<?php

// Local provisioning only. Credentials are stored under ignored .local/.
$root = dirname(__DIR__);
$credentialFile = $root.'/.local/mysql-credentials.json';
if (file_exists($credentialFile)) {
    $credentials = json_decode(file_get_contents($credentialFile), true, flags: JSON_THROW_ON_ERROR);
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', $credentials['root_password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} else {
    $credentials = ['root_password' => bin2hex(random_bytes(24)), 'app_password' => bin2hex(random_bytes(24)), 'test_password' => bin2hex(random_bytes(24))];
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    foreach (['sipas_db', 'sipas_test'] as $database) {
        $pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
    foreach (['sipas_app' => ['sipas_db', 'app_password'], 'sipas_tester' => ['sipas_test', 'test_password']] as $user => [$database, $key]) {
        $password = $pdo->quote($credentials[$key]);
        $pdo->exec("CREATE USER '$user'@'localhost' IDENTIFIED BY $password");
        $pdo->exec("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, REFERENCES ON `$database`.* TO '$user'@'localhost'");
    }
    $pdo->exec("ALTER USER 'root'@'localhost' IDENTIFIED BY ".$pdo->quote($credentials['root_password']));
    file_put_contents($credentialFile, json_encode($credentials, JSON_PRETTY_PRINT));
}
$testing = "APP_ENV=testing\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=sipas_test\nDB_USERNAME=sipas_tester\nDB_PASSWORD=".$credentials['test_password']."\nCACHE_STORE=array\nSESSION_DRIVER=array\nQUEUE_CONNECTION=sync\n";
if (! isset($credentials['ui_password'])) {
    $credentials['ui_password'] = bin2hex(random_bytes(24));
    $pdo->exec('CREATE DATABASE sipas_ui_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec("CREATE USER 'sipas_ui_tester'@'localhost' IDENTIFIED BY ".$pdo->quote($credentials['ui_password']));
    $pdo->exec("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, REFERENCES ON sipas_ui_test.* TO 'sipas_ui_tester'@'localhost'");
    file_put_contents($credentialFile, json_encode($credentials, JSON_PRETTY_PRINT));
    $ui = new PDO('mysql:host=127.0.0.1;port=3306;dbname=sipas_ui_test;charset=utf8mb4', 'sipas_ui_tester', $credentials['ui_password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $ui->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($pdo->query('SHOW TABLES FROM sipas_db')->fetchAll(PDO::FETCH_COLUMN) as $table) {
        $ui->exec($pdo->query('SHOW CREATE TABLE sipas_db.`'.$table.'`')->fetch(PDO::FETCH_NUM)[1]);
    }
    $ui->exec('SET FOREIGN_KEY_CHECKS=1');
}
preg_match('/^APP_KEY=(.*)$/m', file_get_contents($root.'/.env'), $keyMatch);
$testing .= 'APP_KEY='.trim($keyMatch[1] ?? '')."\n";
file_put_contents($root.'/.env.testing', $testing);
echo 'Server: '.$pdo->query('SELECT VERSION()')->fetchColumn().PHP_EOL;
echo 'Database aplikasi dan pengujian siap; kredensial disimpan lokal.'.PHP_EOL;
