<?php

require __DIR__.'/test_mysql_environment.php';
if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/assets/__ocr_test.png') {
    header('Content-Type: image/png');
    readfile(dirname(__DIR__).'/.local/ocr-sample.png');

    return true;
}
$public = dirname(__DIR__).'/public';
$path = $public.rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (is_file($path) && str_starts_with(realpath($path), realpath($public).DIRECTORY_SEPARATOR)) {
    return false;
}
require $public.'/index.php';
