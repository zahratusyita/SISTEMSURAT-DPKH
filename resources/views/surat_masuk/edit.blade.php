@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="javascript:history.back()" class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 hover:text-indigo-600 transition-all" title="Kembali">
        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
    </a>
    <div>
        <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">Edit Data Surat Masuk</h1>
        <p class="mt-0.5 text-xs sm:text-sm text-slate-500 hidden sm:block">Perbarui informasi surat yang sudah tersimpan.</p>
    </div>
</div>

<form action="{{ route('surat-masuk.update', $surat_masuk->id) }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3 items-start">
    @csrf
    @method('PUT')

    <!-- Kolom Kiri: Form Data -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Section: Data Administrasi -->
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Data Administrasi</h2>
            </div>
            
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="tanggal_masuk_surat" class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Masuk Surat <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_masuk_surat" id="tanggal_masuk_surat" value="{{ old('tanggal_masuk_surat', $surat_masuk->tanggal_masuk_surat) }}" required class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div>
                    <label for="nomor_urut" class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Urut</label>
                    <input type="text" name="nomor_urut" id="nomor_urut" value="{{ old('nomor_urut', $surat_masuk->nomor_urut) }}" placeholder="Contoh: 001" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
            </div>
        </section>

        <!-- Section: Detail Surat -->
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Detail Surat Asli</h2>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="alamat_pengirim" class="block text-sm font-semibold text-slate-700 mb-1.5">Instansi / Pengirim</label>
                    <input type="text" name="alamat_pengirim" id="alamat_pengirim" value="{{ old('alamat_pengirim', $surat_masuk->alamat_pengirim) }}" placeholder="Ketik nama instansi pengirim" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div>
                    <label for="tanggal_surat" class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_surat" id="tanggal_surat" value="{{ old('tanggal_surat', $surat_masuk->tanggal_surat) }}" required class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div>
                    <label for="nomor_surat" class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Surat Asli</label>
                    <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat', $surat_masuk->nomor_surat) }}" placeholder="Berdasarkan dokumen fisik" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div class="sm:col-span-2">
                    <label for="perihal" class="block text-sm font-semibold text-slate-700 mb-1.5">Perihal / Isi Ringkas <span class="text-rose-500">*</span></label>
                    <textarea id="perihal" name="perihal" rows="3" required placeholder="Jelaskan secara singkat perihal surat ini..." class="block w-full rounded-xl border-0 bg-slate-50/50 py-3 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">{{ old('perihal', $surat_masuk->perihal) }}</textarea>
                </div>
            </div>
        </section>

        <!-- Section: Disposisi -->
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Tujuan Disposisi</h2>
            </div>
            
            <div>
                <label for="tujuan_disposisi" class="block text-sm font-semibold text-slate-700 mb-1.5">Diteruskan Kepada</label>
                <input type="text" name="tujuan_disposisi" id="tujuan_disposisi" value="{{ old('tujuan_disposisi', $surat_masuk->tujuan_disposisi) }}" placeholder="Contoh: Kepala Bagian Umum" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
            </div>
        </section>
    </div>

    <!-- Kolom Kanan: Sidebar Lampiran & Action -->
    <div class="space-y-6">
        <aside class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 sticky top-24">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Lampiran File</h2>
            </div>

            @if($surat_masuk->lampiran_path)
                <!-- Existing Attachment -->
                <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                            <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800" title="{{ $surat_masuk->lampiran_nama_asli }}">{{ $surat_masuk->lampiran_nama_asli }}</p>
                            <p class="text-xs text-slate-500">{{ $surat_masuk->lampiran_ukuran_label ?: 'Ukuran tidak diketahui' }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex flex-col gap-2">
                        <a href="{{ $surat_masuk->lampiran_url }}" target="_blank" class="flex w-full items-center justify-center rounded-lg bg-white px-3 py-2 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-50 transition-colors">
                            Lihat Dokumen Saat Ini
                        </a>
                        <label class="group flex cursor-pointer items-start gap-2 rounded-lg border border-rose-200 bg-rose-50/50 p-2.5 hover:bg-rose-50 transition-colors">
                            <div class="flex h-5 items-center">
                                <input type="checkbox" name="hapus_lampiran" value="1" class="h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-600">
                            </div>
                            <div class="text-xs font-semibold text-rose-700 leading-tight mt-0.5">
                                Hapus lampiran ini secara permanen saat disimpan
                            </div>
                        </label>
                    </div>
                </div>
            @endif

            <!-- Upload New -->
            <div>
                <label for="lampiran" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $surat_masuk->lampiran_path ? 'Ganti dengan File Baru' : 'Unggah Dokumen' }}</label>
                <div class="mt-2 flex justify-center rounded-xl border border-dashed border-slate-300 px-6 py-8 hover:border-indigo-500 hover:bg-indigo-50/50 transition-colors bg-slate-50/50">
                    <div class="text-center">
                        <svg class="mx-auto h-10 w-10 text-slate-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M11.47 2.47a.75.75 0 011.06 0l4.5 4.5a.75.75 0 01-1.06 1.06l-3.22-3.22V16.5a.75.75 0 01-1.5 0V4.81L8.03 8.03a.75.75 0 01-1.06-1.06l4.5-4.5zM3 15.75a.75.75 0 01.75.75v2.25a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5V16.5a.75.75 0 011.5 0v2.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V16.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                        </svg>
                        <div class="mt-4 flex text-sm leading-6 text-slate-600 justify-center">
                            <label for="lampiran" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                <span>Pilih file</span>
                                <input id="lampiran" name="lampiran" type="file" class="sr-only" accept=".pdf,image/jpeg,image/png,image/webp">
                            </label>
                            <p class="pl-1">atau seret ke sini</p>
                        </div>
                        <p class="text-xs leading-5 text-slate-500">PDF, PNG, JPG hingga 10MB</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col gap-3">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Simpan Perubahan
                </button>
                <a href="javascript:history.back()" class="w-full inline-flex items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-colors">
                    Batalkan
                </a>
            </div>
        </aside>
    </div>
</form>
@endsection
