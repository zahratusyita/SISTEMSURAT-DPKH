<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratKeluarImportController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratMasukImportController;
use App\Http\Controllers\SuratMasukLookupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/admins', [ProfileController::class, 'storeAdmin'])->name('profile.admins.store');
    Route::delete('/profile/admins/{user}', [ProfileController::class, 'destroyAdmin'])->name('profile.admins.destroy');

    Route::get('/surat-masuk/export', [SuratMasukController::class, 'export'])->name('surat-masuk.export');
    Route::get('/surat-masuk/pilihan-balasan', SuratMasukLookupController::class)->name('surat-masuk.lookup');
    Route::get('/surat-masuk/import', [SuratMasukImportController::class, 'create'])->name('surat-masuk.import');
    Route::post('/surat-masuk/import', [SuratMasukImportController::class, 'store'])->name('surat-masuk.import.store')->block(30, 30);
    Route::delete('/surat-masuk/reset', [SuratMasukController::class, 'reset'])->name('surat-masuk.reset');
    Route::resource('surat-masuk', SuratMasukController::class)->parameters([
        'surat-masuk' => 'surat_masuk',
    ]);

    Route::get('/surat-keluar/export', [SuratKeluarController::class, 'export'])->name('surat-keluar.export');
    Route::get('/surat-keluar/import', [SuratKeluarImportController::class, 'create'])->name('surat-keluar.import');
    Route::post('/surat-keluar/import', [SuratKeluarImportController::class, 'store'])->name('surat-keluar.import.store')->block(30, 30);
    Route::delete('/surat-keluar/reset', [SuratKeluarController::class, 'reset'])->name('surat-keluar.reset');
    Route::resource('surat-keluar', SuratKeluarController::class)->parameters([
        'surat-keluar' => 'surat_keluar',
    ]);
});
