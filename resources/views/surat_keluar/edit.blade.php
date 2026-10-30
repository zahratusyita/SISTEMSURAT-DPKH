@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="javascript:history.back()" class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 hover:text-indigo-600 transition-all" title="Kembali">
        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
    </a>
    <div>
        <div class="flex items-center gap-2">
            <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">Edit Surat Keluar</h1>
            <span class="inline-flex items-center justify-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                #{{ $surat_keluar->nomor_urut ?: 'TBA' }}
            </span>
        </div>
        <p class="mt-0.5 text-xs sm:text-sm text-slate-500 hidden sm:block">Perbarui informasi dan lampiran dokumen surat keluar.</p>
    </div>
</div>

<form action="{{ route('surat-keluar.update', $surat_keluar->id) }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3 items-start">
    @method('PUT')
    @include('surat_keluar.form', ['buttonLabel' => 'Perbarui Data'])
</form>
@endsection
