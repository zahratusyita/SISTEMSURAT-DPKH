@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-start justify-between gap-3">
    <div class="flex items-center gap-3">
        <a href="javascript:history.back()" class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 hover:text-indigo-600 transition-all" title="Kembali">
            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">Detail Surat Masuk</h1>
                <span class="inline-flex items-center justify-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                    #{{ $surat_masuk->nomor_urut ?: 'TBA' }}
                </span>
            </div>
            <p class="mt-0.5 text-xs sm:text-sm text-slate-500 hidden sm:block">Tinjau informasi lengkap dan lampiran dokumen surat.</p>
        </div>
    </div>
    <a href="{{ route('surat-masuk.edit', $surat_masuk->id) }}" class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-3 py-2 sm:px-4 sm:py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-indigo-700 transition-all">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
        <span class="hidden sm:inline">Edit Data</span>
        <span class="sm:hidden">Edit</span>
    </a>
</div>

<div class="grid gap-5 sm:gap-6 lg:grid-cols-3 items-start">
    <!-- Main Info Section -->
    <section class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 lg:p-8 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
        <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">Rincian Informasi</h2>
        </div>
        
        <div class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <!-- Tgl Masuk -->
            <div class="rounded-xl bg-slate-50/50 p-4 ring-1 ring-slate-100 transition-colors hover:bg-slate-50">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Tanggal Masuk</p>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($surat_masuk->tanggal_masuk_surat)->format('d F Y') }}</p>
                </div>
            </div>
            
            <!-- Tgl Surat -->
            <div class="rounded-xl bg-slate-50/50 p-4 ring-1 ring-slate-100 transition-colors hover:bg-slate-50">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Tanggal Surat</p>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($surat_masuk->tanggal_surat)->format('d F Y') }}</p>
                </div>
            </div>

            <!-- Nomor Surat -->
            <div class="rounded-xl bg-slate-50/50 p-4 ring-1 ring-slate-100 transition-colors hover:bg-slate-50 sm:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Nomor Surat Asli</p>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" /></svg>
                    <p class="text-sm font-bold text-slate-800">{{ $surat_masuk->nomor_surat ?: '-' }}</p>
                </div>
            </div>

            <!-- Pengirim -->
            <div class="rounded-xl bg-slate-50/50 p-4 ring-1 ring-slate-100 transition-colors hover:bg-slate-50 sm:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Instansi / Pengirim</p>
                <div class="flex items-start gap-2">
                    <svg class="h-4 w-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    <p class="text-sm font-semibold text-slate-800">{{ $surat_masuk->alamat_pengirim ?: '-' }}</p>
                </div>
            </div>

            <!-- Tujuan / Disposisi -->
            <div class="rounded-xl bg-slate-50/50 p-4 ring-1 ring-slate-100 transition-colors hover:bg-slate-50 sm:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Tujuan / Disposisi</p>
                <div class="flex items-start gap-2">
                    <svg class="h-4 w-4 text-sky-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    <p class="text-sm font-semibold text-slate-800">{{ $surat_masuk->tujuan_disposisi ?: '-' }}</p>
                </div>
            </div>

            <!-- Perihal -->
            <div class="rounded-xl bg-indigo-50/30 p-5 ring-1 ring-indigo-100 sm:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500 mb-2">Perihal / Isi Ringkas</p>
                <p class="whitespace-pre-line text-sm font-medium text-slate-800 leading-relaxed">{{ $surat_masuk->perihal }}</p>
            </div>
        </div>
    </section>

    <!-- Attachment Sidebar -->
    <aside class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm ring-1 ring-slate-100 lg:sticky lg:top-24">
        <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">Lampiran File</h2>
        </div>
        
        @if($surat_masuk->lampiran_path)
            <div class="group relative rounded-xl border border-slate-200 bg-slate-50 p-5 hover:bg-slate-100 transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 group-hover:scale-105 transition-transform">
                        <svg class="h-8 w-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <p class="break-words text-sm font-bold text-slate-800 line-clamp-2" title="{{ $surat_masuk->lampiran_nama_asli }}">
                        {{ $surat_masuk->lampiran_nama_asli }}
                    </p>
                    <p class="mt-2 inline-flex items-center rounded-full bg-slate-200/60 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                        {{ $surat_masuk->lampiran_ukuran_label ?: 'Ukuran tidak diketahui' }}
                    </p>
                </div>
                
                <a href="{{ $surat_masuk->lampiran_url }}" target="_blank" class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-700 transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Lihat Dokumen
                </a>
            </div>
        @else
            <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 py-10 px-6 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 mb-3">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Tidak ada lampiran</h3>
                <p class="mt-1 text-xs text-slate-500">File fisik atau scan belum diunggah untuk surat ini.</p>
            </div>
        @endif
    </aside>
</div>

@include('partials.riwayat-surat', ['surat' => $surat_masuk])
<section class="mt-6 rounded-2xl bg-white p-5 sm:p-6 shadow-sm ring-1 ring-slate-100">
    <h2 class="text-lg font-bold text-slate-800">Surat Keluar Balasan</h2>
    <ul class="mt-4 divide-y divide-slate-100">
        @forelse($balasan as $suratBalasan)
        <li class="py-3"><a href="{{ route('surat-keluar.show', $suratBalasan) }}" class="font-semibold text-indigo-700 hover:underline">{{ $suratBalasan->nomor_surat ?: 'Tanpa nomor' }} - {{ $suratBalasan->perihal }}</a>
            <p class="mt-1 text-xs text-slate-500">{{ \Carbon\Carbon::parse($suratBalasan->tanggal_keluar_surat)->format('d/m/Y') }}</p>
        </li>
        @empty<li class="py-3 text-sm text-slate-500">Belum ada surat keluar yang dicatat sebagai balasan.</li>@endforelse
    </ul>
    <div class="mt-3">{{ $balasan->links() }}</div>
</section>
@endsection
