<?php

use App\Services\AttachmentCleanup;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sipas:cleanup-attachments', function () {
    $remaining = app(AttachmentCleanup::class)->retry();
    $this->info('Lampiran menunggu cleanup: '.$remaining);

    return $remaining === 0 ? 0 : 1;
})->purpose('Mengulang pembersihan berkas lampiran yang gagal');

Schedule::command('sipas:cleanup-attachments')->hourly()->withoutOverlapping();
