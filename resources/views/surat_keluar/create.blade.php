@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-4">
        <a href="javascript:history.back()" class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 hover:text-indigo-600 transition-all" title="Kembali">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Data Surat Keluar</h1>
            <p class="mt-1 text-sm text-slate-500">Isi formulir untuk meregistrasi surat keluar baru.</p>
        </div>
    </div>
</div>

<form action="{{ route('surat-keluar.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3 items-start">
    @include('surat_keluar.form', ['buttonLabel' => 'Simpan Data'])
</form>
@endsection
