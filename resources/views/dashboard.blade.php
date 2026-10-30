@extends('layouts.app')

@section('content')

{{-- ============================================================ --}}
{{-- Page Header — Responsif Mobile                              --}}
{{-- ============================================================ --}}
<div class="mb-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Dashboard Overview</h1>
            <p class="mt-1 text-sm text-slate-500 font-medium">Ringkasan statistik arsip surat masuk/keluar terkini.</p>
        </div>
        {{-- Quick Action Buttons - full width di mobile --}}
        <div class="flex flex-col xs:flex-row gap-2 sm:gap-3">
            <a href="{{ route('surat-masuk.create') }}" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-indigo-700 transition-all duration-300 active:scale-95">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Surat Masuk</span>
            </a>
            <a href="{{ route('surat-keluar.create') }}" class="flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 transition-all duration-300 active:scale-95">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                <span>Surat Keluar</span>
            </a>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- Summary Cards — 2 kolom di mobile, 4 di desktop             --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">

    {{-- Card 1: Surat Masuk Bulan Ini --}}
    <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-slate-100 group hover:shadow-lg transition-all duration-300 border-t-4 border-t-blue-500">
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-blue-50/50 transition-transform duration-500 group-hover:scale-150"></div>
        <div class="relative flex items-center justify-between mb-3 sm:mb-4">
            <div class="flex h-9 w-9 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
            <span class="hidden sm:inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-600 border border-blue-100">Bulan Ini</span>
        </div>
        <div class="relative">
            <p class="text-[11px] sm:text-sm font-bold text-slate-500 uppercase tracking-wide leading-tight">Surat Masuk</p>
            <p class="text-xs text-slate-400 sm:hidden">Bulan ini</p>
            <div class="mt-1 flex items-baseline gap-1.5">
                <p class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">{{ $totalBulanIni }}</p>
                <span class="text-xs sm:text-sm font-semibold text-slate-400">Berkas</span>
            </div>
        </div>
    </div>

    {{-- Card 2: Surat Keluar Bulan Ini --}}
    <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-slate-100 group hover:shadow-lg transition-all duration-300 border-t-4 border-t-emerald-500">
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-emerald-50/50 transition-transform duration-500 group-hover:scale-150"></div>
        <div class="relative flex items-center justify-between mb-3 sm:mb-4">
            <div class="flex h-9 w-9 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </div>
            <span class="hidden sm:inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 border border-emerald-100">Bulan Ini</span>
        </div>
        <div class="relative">
            <p class="text-[11px] sm:text-sm font-bold text-slate-500 uppercase tracking-wide leading-tight">Surat Keluar</p>
            <p class="text-xs text-slate-400 sm:hidden">Bulan ini</p>
            <div class="mt-1 flex items-baseline gap-1.5">
                <p class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">{{ $totalKeluarBulanIni }}</p>
                <span class="text-xs sm:text-sm font-semibold text-slate-400">Berkas</span>
            </div>
        </div>
    </div>

    {{-- Card 3: Total Masuk Tahun Ini --}}
    <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-slate-100 group hover:shadow-lg transition-all duration-300 border-t-4 border-t-amber-500">
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-amber-50/50 transition-transform duration-500 group-hover:scale-150"></div>
        <div class="relative flex items-center justify-between mb-3 sm:mb-4">
            <div class="flex h-9 w-9 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
            <span class="hidden sm:inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-600 border border-amber-100">Tahun Ini</span>
        </div>
        <div class="relative">
            <p class="text-[11px] sm:text-sm font-bold text-slate-500 uppercase tracking-wide leading-tight">Total Masuk</p>
            <p class="text-xs text-slate-400 sm:hidden">Tahun ini</p>
            <div class="mt-1 flex items-baseline gap-1.5">
                <p class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">{{ $totalTahunIni }}</p>
                <span class="text-xs sm:text-sm font-semibold text-slate-400">Berkas</span>
            </div>
        </div>
    </div>

    {{-- Card 4: Total Keseluruhan Arsip --}}
    <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-slate-100 group hover:shadow-lg transition-all duration-300 border-t-4 border-t-indigo-600">
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-indigo-50/50 transition-transform duration-500 group-hover:scale-150"></div>
        <div class="relative flex items-center justify-between mb-3 sm:mb-4">
            <div class="flex h-9 w-9 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                </svg>
            </div>
            <span class="hidden sm:inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 border border-indigo-100">Semua Arsip</span>
        </div>
        <div class="relative">
            <p class="text-[11px] sm:text-sm font-bold text-slate-500 uppercase tracking-wide leading-tight">Total Arsip</p>
            <p class="text-xs text-slate-400 sm:hidden">Semua waktu</p>
            <div class="mt-1 flex items-baseline gap-1.5">
                <p class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">{{ $totalSemua }}</p>
                <span class="text-xs sm:text-sm font-semibold text-slate-400">Berkas</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- Chart + Recent Activity — stack di mobile, grid di desktop   --}}
{{-- ============================================================ --}}
<div class="mt-5 sm:mt-8 grid gap-5 sm:gap-6 lg:grid-cols-3">

    {{-- Chart Section --}}
    <section class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
        <div class="flex items-center justify-between mb-4 sm:mb-8">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Tren Surat Masuk</h2>
                <p class="text-xs sm:text-sm text-slate-500">Statistik bulanan tahun {{ date('Y') }}</p>
            </div>
            <div class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                {{ date('Y') }}
            </div>
        </div>
        
        {{-- Bar chart — lebih ringkas di mobile --}}
        <div class="mt-2 grid grid-cols-6 sm:grid-cols-12 gap-1.5 sm:gap-3 h-48 sm:h-64 items-end">
            @php 
                $bulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des']; 
                $maxTotal = max(1, count($statistikBulanan) > 0 ? max($statistikBulanan->toArray()) : 1);
            @endphp
            
            @foreach($bulan as $key => $label)
                @php 
                    $total = (int) ($statistikBulanan[$key] ?? 0); 
                    $percentage = min(100, ($total / $maxTotal) * 100);
                    if($total > 0 && $percentage < 10) $percentage = 10;
                @endphp
                <div class="flex flex-col items-center gap-1.5 sm:gap-3 relative group">
                    {{-- Tooltip --}}
                    <div class="absolute -top-9 left-1/2 -translate-x-1/2 z-10 scale-0 rounded-lg bg-slate-800 px-2 py-1 text-xs text-white opacity-0 transition-all group-hover:scale-100 group-hover:opacity-100 whitespace-nowrap shadow-lg">
                        {{ $total }} Surat
                    </div>
                    
                    <div class="flex h-36 sm:h-48 w-full justify-center items-end rounded-t-lg bg-slate-50 px-0.5 sm:px-1 pb-1 relative overflow-hidden ring-1 ring-slate-100">
                        @if($total > 0)
                            <div class="w-full rounded-sm sm:rounded-md bg-gradient-to-t from-indigo-600 to-cyan-400 transition-all duration-700 hover:opacity-80" style="height: {{ $percentage }}%"></div>
                        @else
                            <div class="w-full rounded-sm sm:rounded-md bg-slate-200" style="height: 2%"></div>
                        @endif
                    </div>
                    <span class="text-[9px] sm:text-[11px] font-semibold text-slate-500">{{ $label }}</span>
                </div>
            @endforeach
        </div>

        {{-- Mobile: label tambahan supaya chart lebih mudah dibaca --}}
        <p class="mt-3 text-center text-[10px] text-slate-400 sm:hidden">Geser dan tahan kolom untuk melihat jumlah</p>
    </section>

    {{-- Aktivitas Terbaru --}}
    <section class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-slate-100 flex flex-col">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Aktivitas Terbaru</h2>
            <a href="{{ route('surat-masuk.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                Lihat Semua →
            </a>
        </div>
        
        <div class="flex-1 space-y-2 sm:space-y-4">
            @forelse($suratTerbaru as $surat)
                <a href="{{ $surat['route'] }}" class="group flex items-start gap-3 rounded-xl border border-transparent p-2 transition-all hover:bg-slate-50 hover:border-slate-100 active:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{-- Ikon jenis surat --}}
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $surat['jenis'] === 'keluar' ? 'bg-emerald-50 text-emerald-500 ring-emerald-100' : 'bg-blue-50 text-blue-500 ring-blue-100' }} ring-1 group-hover:{{ $surat['jenis'] === 'keluar' ? 'bg-emerald-500' : 'bg-blue-500' }} group-hover:text-white transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            @if($surat['jenis'] === 'keluar')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            @endif
                        </svg>
                    </div>
                    
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="truncate text-sm font-semibold text-slate-800 group-hover:text-indigo-700 leading-tight" title="{{ $surat['perihal'] }}">{{ $surat['perihal'] }}</p>
                            <span class="shrink-0 rounded-full {{ $surat['jenis'] === 'keluar' ? 'bg-emerald-50 text-emerald-700 ring-emerald-100' : 'bg-blue-50 text-blue-700 ring-blue-100' }} px-1.5 py-0.5 text-[9px] font-bold uppercase ring-1">
                                {{ $surat['jenis'] }}
                            </span>
                        </div>
                        <p class="truncate text-xs text-slate-500 mt-0.5">{{ $surat['pihak'] }}</p>
                        <p class="mt-1 text-[10px] font-bold text-indigo-400 uppercase tracking-wider">{{ \Carbon\Carbon::parse($surat['tanggal'])->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <div class="flex h-full flex-col items-center justify-center text-center p-6 border-2 border-dashed border-slate-200 rounded-xl">
                    <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7.598a18.18 18.18 0 012.726-9.286 18.217 18.217 0 013.204-2.793 18.06 18.06 0 013.112-1.521A18.35 18.35 0 0112 3c1.785 0 3.525.26 5.176.758a18.06 18.06 0 013.112 1.521 18.217 18.217 0 013.204 2.793 18.18 18.18 0 012.726 9.286V21A2.25 2.25 0 0119.5 23.25h-15A2.25 2.25 0 012.25 21V13.5z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Belum Ada Data</p>
                    <p class="text-xs text-slate-500 mt-1">Surat yang baru dicatat akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Footer link — tetap ada di mobile --}}
        <div class="mt-4 sm:mt-6 pt-4 border-t border-slate-100">
            <a href="{{ route('surat-masuk.index') }}" class="flex items-center justify-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors group py-1">
                Lihat Semua Agenda
                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </section>
</div>

{{-- ============================================================ --}}
{{-- Quick Access Links — khusus mobile (shortcut bawah halaman)  --}}
{{-- ============================================================ --}}
<div class="mt-5 grid grid-cols-2 gap-3 sm:hidden">
    <a href="{{ route('surat-masuk.index') }}" class="flex flex-col items-center justify-center gap-2 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 text-center hover:bg-indigo-50 transition-colors active:scale-95">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
        </div>
        <span class="text-xs font-bold text-slate-700">Agenda Masuk</span>
    </a>
    <a href="{{ route('surat-keluar.index') }}" class="flex flex-col items-center justify-center gap-2 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 text-center hover:bg-emerald-50 transition-colors active:scale-95">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
        </div>
        <span class="text-xs font-bold text-slate-700">Agenda Keluar</span>
    </a>
</div>

@endsection
