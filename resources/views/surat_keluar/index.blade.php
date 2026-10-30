@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Agenda Surat Keluar</h1>
            <p class="mt-1 text-sm text-slate-500 font-medium hidden sm:block">Manajemen dan rekapitulasi seluruh dokumen surat keluar.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('surat-keluar.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-3 py-2 sm:px-4 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-indigo-700 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Tambah</span>
            </a>
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('surat-keluar.import') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-all">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    Import
                </a>
                <a id="suratKeluarExportLink" data-export-base="{{ route('surat-keluar.export') }}" href="{{ route('surat-keluar.export', ['search' => request('search'), 'bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 shadow-sm ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100 transition-all">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Export
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar & Filters -->
<div class="mb-6 bg-white p-4 lg:p-5 shadow-sm ring-1 ring-slate-100 rounded-2xl">
    <form id="suratKeluarFilterForm" action="{{ route('surat-keluar.index') }}" method="GET" data-turbo="false" data-ajax-filter data-results-target="#suratKeluarResults" data-export-link="#suratKeluarExportLink" data-reset-link="#suratKeluarResetLink" data-default-bulan="{{ date('m') }}" data-default-tahun="{{ date('Y') }}" class="flex flex-col gap-4 lg:flex-row lg:items-center w-full" x-data="{ submitForm() { $el.requestSubmit(); } }">
        <label for="search" class="sr-only">Cari</label>
        <div class="relative w-full lg:flex-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </div>
            <input type="text" name="search" id="search" value="{{ request('search') }}" @input.debounce.500ms="submitForm()" class="block w-full rounded-xl border-0 py-2.5 pl-11 pr-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium bg-slate-50 hover:bg-white transition-colors" placeholder="Ketik untuk mencari otomatis...">
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            @php
                $bulanOptions = [
                    ['value' => 'all', 'label' => 'Semua Bulan'],
                    ['value' => '01', 'label' => 'Januari'],
                    ['value' => '02', 'label' => 'Februari'],
                    ['value' => '03', 'label' => 'Maret'],
                    ['value' => '04', 'label' => 'April'],
                    ['value' => '05', 'label' => 'Mei'],
                    ['value' => '06', 'label' => 'Juni'],
                    ['value' => '07', 'label' => 'Juli'],
                    ['value' => '08', 'label' => 'Agustus'],
                    ['value' => '09', 'label' => 'September'],
                    ['value' => '10', 'label' => 'Oktober'],
                    ['value' => '11', 'label' => 'November'],
                    ['value' => '12', 'label' => 'Desember'],
                ];
                
                $tahunOptions = [['value' => 'all', 'label' => 'Semua Tahun']];
                $currentYear = date('Y');
                for($i = $currentYear; $i >= 2020; $i--) {
                    $tahunOptions[] = ['value' => (string)$i, 'label' => (string)$i];
                }
            @endphp
            
            <div class="w-full sm:w-40 z-20">
                <x-custom-select name="bulan" :options="$bulanOptions" :selected="$filterBulan" onchange="submitForm()" />
            </div>
            
            <div class="w-full sm:w-36 z-10">
                <x-custom-select name="tahun" :options="$tahunOptions" :selected="$filterTahun" onchange="submitForm()" />
            </div>
            
                <a id="suratKeluarResetLink" href="{{ route('surat-keluar.index') }}" class="{{ request('search') || $filterBulan !== date('m') || $filterTahun !== date('Y') ? '' : 'hidden' }} inline-flex items-center justify-center rounded-xl bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-600 shadow-sm ring-1 ring-inset ring-rose-200 hover:bg-rose-100 transition-all">
                    Reset
                </a>
        </div>
    </form>
</div>

<!-- Main Table Modern Design with Original Excel Structure -->
<div id="suratKeluarResults" class="transition-opacity duration-150">

{{-- MOBILE: Card List View (sm:hidden) --}}
<div class="sm:hidden space-y-3">
    @forelse($surat_keluar as $surat)
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-800 leading-snug line-clamp-2">{{ $surat->perihal }}</p>
                    <p class="mt-1 text-xs text-slate-500 truncate">{{ $surat->alamat_penerima ?: 'Penerima tidak diisi' }}</p>
                </div>
                <span class="shrink-0 inline-flex items-center rounded-lg bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                    #{{ $surat->nomor_urut ?: '-' }}
                </span>
            </div>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-slate-500">
                <span class="flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    {{ \Carbon\Carbon::parse($surat->tanggal_keluar_surat)->format('d/m/Y') }}
                </span>
                @if($surat->nomor_surat)
                <span class="flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" /></svg>
                    {{ $surat->nomor_surat }}
                </span>
                @endif
                @if($surat->asal)
                <span class="flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    {{ $surat->asal }}
                </span>
                @endif
            </div>
        </div>
        <div class="border-t border-slate-100 px-4 py-2.5 flex items-center justify-between bg-slate-50/50">
            @if($surat->lampiran_path)
            <a href="{{ $surat->lampiran_url }}" target="_blank" class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                Lihat Lampiran
            </a>
            @else
            <span class="text-xs text-slate-400">Tidak ada lampiran</span>
            @endif
            <div class="flex items-center gap-1.5">
                <a href="{{ route('surat-keluar.show', $surat->id) }}" data-turbo-frame="_top" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white transition-colors" title="Detail">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </a>
                <a href="{{ route('surat-keluar.edit', $surat->id) }}" data-turbo-frame="_top" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors" title="Edit">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                </a>
                <form action="{{ route('surat-keluar.destroy', $surat->id) }}" method="POST" class="inline" data-turbo-frame="_top" onsubmit="return confirm('Yakin ingin menghapus data surat ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" title="Hapus">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-slate-100 p-10 flex flex-col items-center text-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 mb-4 ring-8 ring-slate-50/50">
            <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7.598a18.18 18.18 0 012.726-9.286a18.217 18.217 0 013.204-2.793 18.06 18.06 0 013.112-1.521A18.35 18.35 0 0112 3c1.785 0 3.525.26 5.176.758a18.06 18.06 0 013.112 1.521 18.217 18.217 0 013.204 2.793 18.18 18.18 0 012.726 9.286V21A2.25 2.25 0 0119.5 23.25h-15A2.25 2.25 0 012.25 21V13.5z" /></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Belum Ada Surat</h3>
        <p class="text-xs text-slate-500 mt-1">Klik "Tambah" untuk mulai menginput data surat keluar.</p>
    </div>
    @endforelse
</div>

{{-- DESKTOP: Table View (hidden sm:block) --}}
<div class="hidden sm:block bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
    <div class="overflow-x-auto table-scroll">
        <table class="min-w-full divide-y divide-slate-100 text-left align-middle border-collapse relative">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th scope="col" rowspan="2" class="py-4 pl-4 pr-3 text-xs font-bold text-slate-500 uppercase tracking-widest align-middle whitespace-nowrap">Tgl Keluar</th>
                    <th scope="col" rowspan="2" class="px-3 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest align-middle whitespace-nowrap">Nomor Urut</th>
                    <th scope="col" rowspan="2" class="px-3 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest align-middle">Alamat Penerima</th>
                    <th scope="col" colspan="3" class="px-3 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200 bg-slate-100/50">Detail Surat Keluar</th>
                    <th scope="col" rowspan="2" class="px-3 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest align-middle">Asal Surat</th>
                    <th scope="col" rowspan="2" class="px-3 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-widest align-middle">Lampiran</th>
                    <th scope="col" rowspan="2" class="sticky right-0 z-20 bg-slate-50 py-4 pl-3 pr-5 text-center text-xs font-bold text-slate-500 uppercase tracking-widest align-middle border-l border-slate-200/60 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)]">Aksi</th>
                </tr>
                <tr>
                    <th scope="col" class="px-3 py-3 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap bg-slate-50">Tanggal</th>
                    <th scope="col" class="px-3 py-3 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap bg-slate-50">Nomor</th>
                    <th scope="col" class="px-3 py-3 text-xs font-bold text-slate-500 uppercase tracking-widest bg-slate-50">Perihal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($surat_keluar as $surat)
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-semibold text-slate-800">
                        {{ \Carbon\Carbon::parse($surat->tanggal_keluar_surat)->format('d/m/Y') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-center">
                        <span class="inline-flex items-center justify-center min-w-[3rem] rounded-md bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                            {{ $surat->nomor_urut }}
                        </span>
                    </td>
                    <td class="px-3 py-4 text-sm font-medium text-slate-700 max-w-[12rem] truncate" title="{{ $surat->alamat_penerima }}">
                        {{ $surat->alamat_penerima }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-600">
                        {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d/m/Y') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-600">
                        {{ $surat->nomor_surat }}
                    </td>
                    <td class="px-3 py-4 text-sm font-bold text-slate-800 max-w-[15rem] truncate" title="{{ $surat->perihal }}">
                        {{ $surat->perihal }}
                    </td>
                    <td class="px-3 py-4 text-sm font-medium text-slate-600">
                        {{ $surat->asal ?: '-' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-center">
                        @if($surat->lampiran_path)
                            <a href="{{ $surat->lampiran_url }}" target="_blank" class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white ring-1 ring-emerald-200 transition-colors" title="Lihat Lampiran">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-slate-50 text-slate-300 ring-1 ring-slate-200" title="Tidak ada lampiran">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                        @endif
                    </td>
                    <td class="sticky right-0 z-10 bg-white group-hover:bg-slate-50/80 transition-colors border-l border-slate-100 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.02)] whitespace-nowrap py-4 pl-3 pr-5 text-center">
                        <div class="flex items-center justify-center gap-1.5 transition-opacity duration-200">
                            <!-- Detail -->
                            <a href="{{ route('surat-keluar.show', $surat->id) }}" data-turbo-frame="_top" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white transition-colors" title="Detail">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </a>
                            <!-- Edit -->
                            <a href="{{ route('surat-keluar.edit', $surat->id) }}" data-turbo-frame="_top" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors" title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                            </a>
                            <!-- Delete -->
                            <form action="{{ route('surat-keluar.destroy', $surat->id) }}" method="POST" class="inline" data-turbo-frame="_top" onsubmit="return confirm('Yakin ingin menghapus data surat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 mb-4 ring-8 ring-slate-50/50">
                                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7.598a18.18 18.18 0 012.726-9.286a18.217 18.217 0 013.204-2.793 18.06 18.06 0 013.112-1.521A18.35 18.35 0 0112 3c1.785 0 3.525.26 5.176.758a18.06 18.06 0 013.112 1.521 18.217 18.217 0 013.204 2.793 18.18 18.18 0 012.726 9.286V21A2.25 2.25 0 0119.5 23.25h-15A2.25 2.25 0 012.25 21V13.5z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Belum Ada Surat</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Silakan klik "Tambah Data" di pojok kanan atas untuk mulai menginput data surat keluar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- end DESKTOP table --}}

<!-- Pagination -->
@if($surat_keluar->hasPages())
    <div class="mt-4 sm:mt-6">
        {{ $surat_keluar->links() }}
    </div>
@endif
</div>
@include('partials.reset-surat', ['jenis' => 'keluar'])
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-ajax-filter]').forEach((form) => {
        const target = document.querySelector(form.dataset.resultsTarget);
        const exportLink = document.querySelector(form.dataset.exportLink);
        const resetLink = document.querySelector(form.dataset.resetLink);
        let controller = null;

        const paramsFromForm = () => {
            const params = new URLSearchParams(new FormData(form));
            [...params.entries()].forEach(([key, value]) => {
                if (value === '') params.delete(key);
            });
            return params;
        };

        const updateChrome = (url, params) => {
            window.history.pushState({}, '', url);

            if (exportLink) {
                const exportParams = new URLSearchParams(params);
                exportParams.delete('page');
                const query = exportParams.toString();
                exportLink.href = query ? `${exportLink.dataset.exportBase}?${query}` : exportLink.dataset.exportBase;
            }

            if (resetLink) {
                const dirty = (params.get('search') || '') !== ''
                    || (params.get('bulan') || form.dataset.defaultBulan) !== form.dataset.defaultBulan
                    || (params.get('tahun') || form.dataset.defaultTahun) !== form.dataset.defaultTahun;
                resetLink.classList.toggle('hidden', !dirty);
            }
        };

        const loadUrl = async (url, params = null) => {
            if (!target) return;
            controller?.abort();
            controller = new AbortController();
            target.classList.add('opacity-50');

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: controller.signal,
                });
                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nextTarget = doc.querySelector(form.dataset.resultsTarget);

                if (nextTarget) {
                    target.innerHTML = nextTarget.innerHTML;
                    updateChrome(url, params || new URL(url, window.location.origin).searchParams);
                } else {
                    window.location.href = url;
                }
            } catch (error) {
                if (error.name !== 'AbortError') window.location.href = url;
            } finally {
                target.classList.remove('opacity-50');
            }
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const params = paramsFromForm();
            const query = params.toString();
            const url = query ? `${form.action}?${query}` : form.action;
            loadUrl(url, params);
        });

        target?.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link || !link.href.includes('page=')) return;
            event.preventDefault();
            loadUrl(link.href);
        });

        window.addEventListener('popstate', () => loadUrl(window.location.href));
    });
</script>
@endpush
