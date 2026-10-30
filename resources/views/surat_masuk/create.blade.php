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
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Data Surat Masuk</h1>
            </div>
            <p class="mt-1 text-sm text-slate-500">Isi formulir untuk meregistrasi surat masuk baru.</p>
        </div>
    </div>
</div>

<form action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3 items-start">
    @csrf

    <!-- Kolom Kiri: Form Data -->
    <div class="order-2 lg:order-1 lg:col-span-2 space-y-6">
        
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
                    <input type="date" name="tanggal_masuk_surat" id="tanggal_masuk_surat" value="{{ old('tanggal_masuk_surat', date('Y-m-d')) }}" required class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div>
                    <label for="nomor_urut" class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Urut</label>
                    <input type="text" name="nomor_urut" id="nomor_urut" value="{{ old('nomor_urut', $nomorRekomendasi) }}" placeholder="Contoh: 001/IV/2026" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                    <p class="mt-1.5 text-xs text-emerald-600 font-medium">Rekomendasi otomatis tersedia.</p>
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
                    <input type="text" name="alamat_pengirim" id="alamat_pengirim" value="{{ old('alamat_pengirim') }}" placeholder="Ketik nama instansi pengirim" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div>
                    <label for="tanggal_surat" class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_surat" id="tanggal_surat" value="{{ old('tanggal_surat') }}" required class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div>
                    <label for="nomor_surat" class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Surat Asli</label>
                    <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat') }}" placeholder="Berdasarkan dokumen fisik" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
                </div>
                <div class="sm:col-span-2">
                    <label for="perihal" class="block text-sm font-semibold text-slate-700 mb-1.5">Perihal / Isi Ringkas <span class="text-rose-500">*</span></label>
                    <textarea id="perihal" name="perihal" rows="3" required placeholder="Jelaskan secara singkat perihal surat ini..." class="block w-full rounded-xl border-0 bg-slate-50/50 py-3 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">{{ old('perihal') }}</textarea>
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
                <input type="text" name="tujuan_disposisi" id="tujuan_disposisi" value="{{ old('tujuan_disposisi') }}" placeholder="Contoh: Kepala Bagian Umum" class="block w-full rounded-xl border-0 bg-slate-50/50 py-2.5 pl-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm font-medium transition-all hover:bg-slate-50">
            </div>
        </section>
    </div>

    <!-- Kolom Kanan: Sidebar Lampiran & Action -->
    <div class="order-1 lg:order-2 space-y-6">
        <aside class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 sticky top-24">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Unggah & Scan</h2>
            </div>

            <!-- Upload New & OCR -->
            <div id="uploadActionContainer">
                <p class="text-sm font-semibold text-slate-900">Sumber Dokumen</p>
                <p class="mt-1 text-xs text-slate-500">Pilih file yang sudah ada atau ambil foto langsung dari kamera.</p>
                
                <div id="uploadButtons" class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <label for="lampiran" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 hover:border-indigo-300 hover:bg-indigo-50/60 transition-colors group">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-indigo-600 ring-1 ring-slate-200 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5h10.5A2.25 2.25 0 0019.5 17.25V6.75A2.25 2.25 0 0017.25 4.5H6.75A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5z" /></svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-bold text-slate-800">Pilih File</span>
                            <span class="block text-xs text-slate-500">PDF, JPG, PNG sampai 10MB</span>
                        </span>
                        <input id="lampiran" name="lampiran" type="file" class="sr-only" accept=".pdf,image/jpeg,image/png,image/webp">
                    </label>
                    <button type="button" id="cameraButton" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-left hover:border-indigo-300 hover:bg-slate-50 transition-colors group">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 019.186 4.5h5.628a2.31 2.31 0 012.36 1.675l.183.642A2.25 2.25 0 0019.52 8.5h.23A2.25 2.25 0 0122 10.75v7A2.25 2.25 0 0119.75 20h-15A2.25 2.25 0 012.5 17.75v-7A2.25 2.25 0 014.75 8.5h.23a2.25 2.25 0 002.163-1.683l.184-.642z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 13.25a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                        </span>
                        <span>
                            <span class="block text-sm font-bold text-slate-800">Ambil Foto</span>
                            <span class="block text-xs text-slate-500">Scan dokumen pakai kamera</span>
                        </span>
                    </button>
                </div>

                <!-- Selected File Card -->
                <div id="selectedFilePanel" class="hidden mt-4 rounded-xl border-2 border-indigo-500 bg-indigo-50/80 p-4 relative overflow-hidden transition-all duration-300 shadow-md shadow-indigo-500/10">
                    <div class="absolute top-0 right-0 bg-indigo-500 text-white rounded-bl-xl px-3 py-1 text-[10px] font-bold uppercase tracking-wider">
                        Siap Diproses
                    </div>
                    <div class="flex items-start gap-4 mt-2">
                        <!-- Thumbnail -->
                        <div class="flex-shrink-0 h-16 w-16 bg-white rounded-lg flex items-center justify-center border border-indigo-200 overflow-hidden shadow-sm">
                             <img id="fileThumbnail" class="h-full w-full object-cover hidden" alt="Thumbnail">
                             <svg id="fileIcon" class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        </div>
                        <div class="flex-1 min-w-0 pr-2">
                            <p id="selectedFileName" class="truncate text-sm font-bold text-slate-900"></p>
                            <p class="mt-1 text-[11px] text-slate-600 leading-relaxed">Pindai dengan AI untuk mengisi form otomatis atau langsung Simpan Data.</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-indigo-200/60 flex flex-col gap-2">
                         <button type="button" id="scanButton" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white hover:bg-indigo-500 transition-colors shadow-sm focus:ring-4 focus:ring-indigo-500/20">
                             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" /></svg>
                             Pindai Data Gambar (AI)
                         </button>
                         <button type="button" id="resetFileButton" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 border border-slate-200 transition-colors">
                            Hapus & Pilih Ulang
                         </button>
                    </div>
                </div>
                
                <!-- OCR Status -->
                <div id="ocr_status" class="mt-3 hidden rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs font-medium text-amber-800 shadow-sm animate-pulse"></div>
                <textarea id="ocr_text" class="mt-3 hidden w-full rounded-lg border-0 bg-slate-50 p-3 text-xs text-slate-600 ring-1 ring-inset ring-slate-200" rows="3" readonly></textarea>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 hidden pt-6 border-t border-slate-100 lg:flex lg:flex-col gap-3">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Simpan Data Baru
                </button>
                <a href="javascript:history.back()" class="w-full inline-flex items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-colors">
                    Batalkan
                </a>
            </div>
        </aside>
    </div>

    <div class="order-3 lg:hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <div class="flex flex-col gap-3">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-md shadow-indigo-500/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Simpan Data Baru
            </button>
            <a href="javascript:history.back()" class="w-full inline-flex items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200">
                Batalkan
            </a>
        </div>
    </div>
</form>

<div id="cameraModal" class="fixed inset-0 z-[100] hidden flex-col bg-black h-[100dvh] w-screen">
    <!-- Header -->
    <div class="flex items-center justify-between px-4 py-4 bg-gradient-to-b from-black/80 to-transparent absolute top-0 left-0 right-0 z-10">
        <button type="button" id="cameraClose" class="rounded-full p-2 text-white hover:bg-white/20 transition-colors backdrop-blur-md">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
        <div class="text-center text-white">
            <h2 class="text-sm font-bold tracking-wide">AMBIL FOTO SURAT</h2>
            <p class="text-[10px] text-white/70 uppercase">Posisikan dokumen dalam bingkai</p>
        </div>
        <button type="button" id="cameraSwitch" class="rounded-full p-2 text-white hover:bg-white/20 transition-colors backdrop-blur-md" title="Balik Kamera">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
        </button>
    </div>

    <!-- Viewfinder Container -->
    <div class="flex-1 relative overflow-hidden bg-black flex items-center justify-center">
        <!-- Live Camera View -->
        <video id="cameraVideo" class="h-full w-full object-cover" autoplay playsinline muted></video>
        
        <!-- Photo Preview (Hidden initially) -->
        <img id="cameraPreviewImg" class="h-full w-full object-cover hidden" alt="Preview">

        <!-- Scanner Overlay Guide -->
        <div id="cameraGuide" class="absolute inset-0 pointer-events-none flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md aspect-[3/4] border-2 border-white/40 border-dashed rounded-2xl relative shadow-[0_0_0_4000px_rgba(0,0,0,0.7)]">
                <!-- Corner marks -->
                <div class="absolute top-0 left-0 w-8 h-8 sm:w-12 sm:h-12 border-t-4 border-l-4 border-indigo-400 -mt-0.5 -ml-0.5 rounded-tl-2xl"></div>
                <div class="absolute top-0 right-0 w-8 h-8 sm:w-12 sm:h-12 border-t-4 border-r-4 border-indigo-400 -mt-0.5 -mr-0.5 rounded-tr-2xl"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 sm:w-12 sm:h-12 border-b-4 border-l-4 border-indigo-400 -mb-0.5 -ml-0.5 rounded-bl-2xl"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 sm:w-12 sm:h-12 border-b-4 border-r-4 border-indigo-400 -mb-0.5 -mr-0.5 rounded-br-2xl"></div>
            </div>
        </div>

        <!-- Flash Effect -->
        <div id="cameraFlash" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-200 z-50"></div>
    </div>

    <!-- Status Messages -->
    <div class="absolute bottom-36 left-0 right-0 flex justify-center pointer-events-none z-20">
        <p id="cameraStatus" class="hidden bg-black/70 backdrop-blur-md text-white text-xs px-4 py-2 rounded-full shadow-lg border border-white/10 mx-4 text-center"></p>
    </div>

    <!-- Bottom Controls Area -->
    <div class="h-32 sm:h-40 bg-black flex items-center justify-center px-8 pb-6 pt-2 shrink-0 z-10 relative">
        <!-- Camera Mode Controls -->
        <div id="cameraControls" class="w-full flex items-center justify-center gap-12 max-w-md">
            <!-- Shutter Button -->
            <button type="button" id="cameraShutter" class="group relative flex h-20 w-20 sm:h-24 sm:w-24 items-center justify-center rounded-full bg-transparent border-[3px] border-white focus:outline-none active:scale-95 transition-all">
                <div class="h-[68px] w-[68px] sm:h-[84px] sm:w-[84px] rounded-full bg-white group-active:bg-slate-200 transition-colors"></div>
            </button>
        </div>

        <!-- Preview Mode Controls (Hidden initially) -->
        <div id="previewControls" class="w-full flex items-center justify-between max-w-md hidden">
            <button type="button" id="cameraRetake" class="flex flex-col items-center gap-1.5 text-white hover:text-slate-300 transition-colors">
                <div class="h-14 w-14 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-md">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                </div>
                <span class="text-[11px] font-bold tracking-wide uppercase">Ulangi</span>
            </button>

            <button type="button" id="cameraAccept" class="flex flex-col items-center gap-1.5 text-white hover:text-emerald-300 transition-colors group">
                <div class="h-16 w-16 rounded-full bg-emerald-500 group-hover:bg-emerald-400 flex items-center justify-center shadow-[0_0_20px_rgba(16,185,129,0.4)] transition-all">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <span class="text-[11px] font-bold tracking-wide uppercase text-emerald-400">Gunakan Foto</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const scanButton = document.getElementById('scanButton');
    const imageInput = document.getElementById('lampiran');
    const statusBox = document.getElementById('ocr_status');
    const textBox = document.getElementById('ocr_text');
    const selectedFilePanel = document.getElementById('selectedFilePanel');
    const selectedFileName = document.getElementById('selectedFileName');
    const cameraButton = document.getElementById('cameraButton');
    const cameraModal = document.getElementById('cameraModal');
    const cameraVideo = document.getElementById('cameraVideo');
    const cameraClose = document.getElementById('cameraClose');
    const cameraSwitch = document.getElementById('cameraSwitch');
    const cameraStatus = document.getElementById('cameraStatus');
    const cameraShutter = document.getElementById('cameraShutter');
    const cameraFlash = document.getElementById('cameraFlash');
    const cameraPreviewImg = document.getElementById('cameraPreviewImg');
    const cameraGuide = document.getElementById('cameraGuide');
    const cameraControls = document.getElementById('cameraControls');
    const previewControls = document.getElementById('previewControls');
    const cameraRetake = document.getElementById('cameraRetake');
    const cameraAccept = document.getElementById('cameraAccept');
    
    // New references for file preview & management
    const fileThumbnail = document.getElementById('fileThumbnail');
    const fileIcon = document.getElementById('fileIcon');
    const uploadButtons = document.getElementById('uploadButtons');
    const resetFileButton = document.getElementById('resetFileButton');
    
    let cameraStream = null;
    let currentFacingMode = 'environment';
    let capturedBlob = null;

    const monthMap = {
        januari: '01', februari: '02', maret: '03', april: '04', mei: '05', juni: '06',
        juli: '07', agustus: '08', september: '09', oktober: '10', november: '11', desember: '12'
    };

    function setStatus(message) {
        statusBox.textContent = message;
        statusBox.classList.remove('hidden');
    }

    function setCameraStatus(message) {
        cameraStatus.textContent = message;
        cameraStatus.classList.remove('hidden');
        setTimeout(() => { cameraStatus.classList.add('hidden'); }, 3000);
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        if (cameraVideo) cameraVideo.srcObject = null;
    }

    async function openCamera(facingMode = 'environment') {
        stopCamera();
        currentFacingMode = facingMode;
        
        // Pindahkan modal langsung ke body agar lepas dari parent transform/animation yang bisa merusak `fixed inset-0`
        if (cameraModal && cameraModal.parentNode !== document.body) {
            document.body.appendChild(cameraModal);
        }
        
        // Reset UI
        cameraPreviewImg.classList.add('hidden');
        cameraVideo.classList.remove('hidden');
        cameraGuide.classList.remove('hidden');
        cameraSwitch.classList.remove('hidden');
        previewControls.classList.add('hidden');
        cameraControls.classList.remove('hidden');
        cameraStatus.classList.add('hidden');
        
        cameraModal.classList.remove('hidden');
        cameraModal.classList.add('flex');

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: currentFacingMode } },
                audio: false,
            });
            cameraVideo.srcObject = cameraStream;
        } catch (error) {
            setCameraStatus('Kamera tidak bisa dibuka. Periksa izin kamera.');
        }
    }

    function closeCamera() {
        stopCamera();
        cameraModal.classList.add('hidden');
        cameraModal.classList.remove('flex');
        capturedBlob = null;
    }

    function setLampiranFromBlob(blob, fileName) {
        const file = new File([blob], fileName, { type: 'image/jpeg' });
        const transfer = new DataTransfer();
        transfer.items.add(file);
        imageInput.files = transfer.files;
        showSelectedFile(fileName, blob);
    }

    function showSelectedFile(fileName, fileOrBlob) {
        if (!selectedFilePanel || !selectedFileName) return;
        
        if (fileName) {
            selectedFileName.textContent = fileName;
            selectedFilePanel.classList.remove('hidden');
            if (uploadButtons) uploadButtons.classList.add('hidden');
            
            // Show thumbnail if it's an image
            if (fileOrBlob && fileOrBlob.type.startsWith('image/')) {
                const url = URL.createObjectURL(fileOrBlob);
                if (fileThumbnail) {
                    fileThumbnail.src = url;
                    fileThumbnail.classList.remove('hidden');
                }
                if (fileIcon) fileIcon.classList.add('hidden');
            } else {
                if (fileThumbnail) fileThumbnail.classList.add('hidden');
                if (fileIcon) fileIcon.classList.remove('hidden');
            }

            // Auto-scroll to panel on mobile
            if (window.innerWidth < 1024) {
                setTimeout(() => {
                    selectedFilePanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 150);
            }
        } else {
            selectedFilePanel.classList.add('hidden');
            if (uploadButtons) uploadButtons.classList.remove('hidden');
            if (fileThumbnail && fileThumbnail.src) {
                URL.revokeObjectURL(fileThumbnail.src);
                fileThumbnail.src = '';
            }
        }
    }

    resetFileButton?.addEventListener('click', () => {
        imageInput.value = '';
        showSelectedFile('');
        textBox.classList.add('hidden');
        statusBox.classList.add('hidden');
    });

    function normalizeDate(value) {
        if (!value) return '';
        const text = value.toLowerCase().replace(/[,]/g, ' ').replace(/\s+/g, ' ').trim();
        let match = text.match(/(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})/);
        if (match) {
            const year = match[3].length === 2 ? '20' + match[3] : match[3];
            return `${year}-${match[2].padStart(2, '0')}-${match[1].padStart(2, '0')}`;
        }
        match = text.match(/(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{4})/);
        if (match) {
            return `${match[3]}-${monthMap[match[2]]}-${match[1].padStart(2, '0')}`;
        }
        return '';
    }

    function valueAfterLabel(lines, labels) {
        for (const line of lines) {
            const lower = line.toLowerCase();
            for (const label of labels) {
                if (lower.includes(label)) {
                    const parts = line.split(/:|-/);
                    if (parts.length > 1) return parts.slice(1).join('-').trim();
                }
            }
        }
        return '';
    }

    function fillIfEmpty(id, value) {
        const input = document.getElementById(id);
        if (input && value && !input.value.trim()) input.value = value;
    }

    function parseLetter(text) {
        const lines = text.split(/\r?\n/).map(line => line.trim()).filter(Boolean);
        const nomor = valueAfterLabel(lines, ['nomor', 'no. surat', 'no surat']);
        const perihal = valueAfterLabel(lines, ['perihal', 'hal ']);
        const pengirim = valueAfterLabel(lines, ['dari', 'pengirim']);
        const tanggalLine = lines.find(line => normalizeDate(line));

        fillIfEmpty('nomor_surat', nomor);
        fillIfEmpty('perihal', perihal);
        fillIfEmpty('alamat_pengirim', pengirim);
        fillIfEmpty('tanggal_surat', normalizeDate(tanggalLine || ''));
    }

    cameraButton?.addEventListener('click', () => {
        if (!navigator.mediaDevices?.getUserMedia) {
            setStatus('Browser tidak mendukung akses kamera langsung. Gunakan pilih file biasa.');
            return;
        }
        openCamera('environment');
    });

    cameraClose?.addEventListener('click', closeCamera);
    
    cameraSwitch?.addEventListener('click', () => {
        const newMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        openCamera(newMode);
    });

    cameraShutter?.addEventListener('click', () => {
        if (!cameraStream) {
            setCameraStatus('Kamera belum siap.');
            return;
        }

        // Flash Effect
        cameraFlash.classList.remove('opacity-0');
        cameraFlash.classList.add('opacity-100');
        setTimeout(() => {
            cameraFlash.classList.remove('opacity-100');
            cameraFlash.classList.add('opacity-0');
        }, 150);

        // Capture Image
        const canvas = document.createElement('canvas');
        canvas.width = cameraVideo.videoWidth;
        canvas.height = cameraVideo.videoHeight;
        
        const ctx = canvas.getContext('2d');
        if (currentFacingMode === 'user') {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob((blob) => {
            if (!blob) {
                setCameraStatus('Gagal mengambil foto. Coba ulangi.');
                return;
            }
            
            capturedBlob = blob;
            const imageUrl = URL.createObjectURL(blob);
            cameraPreviewImg.src = imageUrl;
            
            // Switch UI to Preview Mode
            stopCamera();
            cameraVideo.classList.add('hidden');
            cameraPreviewImg.classList.remove('hidden');
            cameraGuide.classList.add('hidden');
            cameraSwitch.classList.add('hidden');
            cameraControls.classList.add('hidden');
            previewControls.classList.remove('hidden');
            
        }, 'image/jpeg', 0.92);
    });

    cameraRetake?.addEventListener('click', () => {
        if (cameraPreviewImg.src) {
            URL.revokeObjectURL(cameraPreviewImg.src);
        }
        openCamera(currentFacingMode);
    });

    cameraAccept?.addEventListener('click', () => {
        if (capturedBlob) {
            setLampiranFromBlob(capturedBlob, 'scan-surat-masuk.jpg');
            closeCamera();
        }
    });

    imageInput?.addEventListener('change', () => {
        showSelectedFile(imageInput.files?.[0]?.name || '', imageInput.files?.[0]);
    });

    scanButton?.addEventListener('click', async () => {
        if (!imageInput.files.length) {
            setStatus('Pilih file scan surat terlebih dahulu.');
            return;
        }

        if (!imageInput.files[0].type.startsWith('image/')) {
            setStatus('Pindai otomatis hanya tersedia untuk file gambar (JPG/PNG/WEBP). PDF akan tetap tersimpan sebagai lampiran biasa.');
            return;
        }

        scanButton.disabled = true;
        const originalText = scanButton.innerHTML;
        scanButton.innerHTML = `<svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memindai...`;
        
        setStatus('Menjalankan AI Scanner lokal. Harap tunggu beberapa saat...');

        try {
            if (!window.SipasOcr) {
                throw new Error('AI Engine (OCR) belum dimuat.');
            }

            const text = await window.SipasOcr.recognize(imageInput.files[0], (message) => {
                if (message.status === 'recognizing text' && message.progress) {
                    setStatus(`AI sedang membaca teks... ${Math.round(message.progress * 100)}%`);
                }
            });
            textBox.value = text;
            textBox.classList.remove('hidden');
            parseLetter(text);
            setStatus('Pemindaian selesai! Field yang berhasil dideteksi telah diisi otomatis. Mohon periksa kembali.');
        } catch (error) {
            console.error('SIPAS OCR failed:', error);
            const detail = error?.message || String(error);
            setStatus(`Gagal membaca dokumen: ${detail}. Pastikan file berupa gambar yang jelas, lalu coba lagi.`);
        } finally {
            scanButton.disabled = false;
            scanButton.innerHTML = originalText;
        }
    });
</script>
@endpush
