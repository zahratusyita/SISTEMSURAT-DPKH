<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalBulanIni = SuratMasuk::whereYear('tanggal_masuk_surat', $today->year)
            ->whereMonth('tanggal_masuk_surat', $today->month)
            ->count();

        $totalTahunIni = SuratMasuk::whereYear('tanggal_masuk_surat', $today->year)->count();
        $totalSemuaMasuk = SuratMasuk::count();
        $totalKeluarBulanIni = SuratKeluar::whereYear('tanggal_keluar_surat', $today->year)
            ->whereMonth('tanggal_keluar_surat', $today->month)
            ->count();
        $totalKeluarTahunIni = SuratKeluar::whereYear('tanggal_keluar_surat', $today->year)->count();
        $totalSemua = $totalSemuaMasuk + SuratKeluar::count();

        $suratMasukTerbaru = SuratMasuk::latest('tanggal_masuk_surat')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (SuratMasuk $surat) => [
                'id' => $surat->id,
                'jenis' => 'masuk',
                'tanggal' => $surat->tanggal_masuk_surat,
                'perihal' => $surat->perihal,
                'pihak' => $surat->alamat_pengirim ?: 'Pengirim tidak dicantumkan',
                'route' => route('surat-masuk.show', $surat),
            ]);

        $suratKeluarTerbaru = SuratKeluar::latest('tanggal_keluar_surat')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (SuratKeluar $surat) => [
                'id' => $surat->id,
                'jenis' => 'keluar',
                'tanggal' => $surat->tanggal_keluar_surat,
                'perihal' => $surat->perihal,
                'pihak' => $surat->alamat_penerima ?: 'Penerima tidak dicantumkan',
                'route' => route('surat-keluar.show', $surat),
            ]);

        $suratTerbaru = $suratMasukTerbaru
            ->merge($suratKeluarTerbaru)
            ->sortByDesc(fn ($surat) => $surat['tanggal'].'-'.str_pad((string) $surat['id'], 10, '0', STR_PAD_LEFT))
            ->take(5)
            ->values();

        // Kompatibel dengan SQLite (lokal) dan MySQL (production)
        $statistikBulanan = collect(range(1, 12))->mapWithKeys(function ($bulan) use ($today) {
            $total = SuratMasuk::whereYear('tanggal_masuk_surat', $today->year)
                ->whereMonth('tanggal_masuk_surat', $bulan)
                ->count();
            return [str_pad((string) $bulan, 2, '0', STR_PAD_LEFT) => $total];
        });

        return view('dashboard', compact(
            'totalBulanIni',
            'totalTahunIni',
            'totalSemua',
            'totalKeluarBulanIni',
            'totalKeluarTahunIni',
            'suratTerbaru',
            'statistikBulanan'
        ));
    }
}
