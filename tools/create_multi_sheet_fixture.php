<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Browser regression fixture only; no database connection or operational writes.
require dirname(__DIR__).'/vendor/autoload.php';
$directory = dirname(__DIR__).'/.local';
if (! is_dir($directory)) {
    mkdir($directory, 0700, true);
}
$key = bin2hex(random_bytes(6));
$book = new Spreadsheet;
$book->getActiveSheet()->setTitle('Sheet1')->fromArray([
    ['tanggal_masuk_surat', 'nomor_urut', 'alamat_pengirim', 'tanggal_surat', 'nomor_surat', 'perihal', 'tujuan_disposisi'],
    ['2026-09-16', 'UI-MULTI-IN-'.$key, 'Instansi Pengujian', '2026-09-16', 'UI-MULTI/IN/'.$key, 'Pengujian impor Sheet1', 'Umum'],
]);
$book->createSheet()->setTitle('Sheet2')->fromArray([
    ['tanggal_keluar_surat', 'nomor_urut', 'alamat_penerima', 'tanggal_surat', 'nomor_surat', 'perihal', 'asal'],
    ['2026-09-17', 'UI-MULTI-OUT-'.$key, 'Instansi Pengujian', '2026-09-17', 'UI-MULTI/OUT/'.$key, 'Pengujian impor Sheet2', 'Umum'],
]);
(new Xlsx($book))->save($directory.'/agenda-dua-sheet.xlsx');
$book->disconnectWorksheets();
echo 'Workbook regresi Sheet1/Sheet2 dibuat di .local.'.PHP_EOL;
