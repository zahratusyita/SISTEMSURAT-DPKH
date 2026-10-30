<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/test_mysql_environment.php';
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (DB::connection()->getDatabaseName() !== 'sipas_ui_test') {
    throw new RuntimeException('UI test hanya diizinkan pada sipas_ui_test.');
}
User::firstOrCreate(['email' => 'ui-test@example.test'], ['name' => 'Administrator Pengujian', 'password' => Hash::make('Ui-test123!')]);
echo 'Akun pengujian browser siap pada sipas_ui_test.'.PHP_EOL;
