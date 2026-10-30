@extends('layouts.app')

@section('content')
@php
    $hasPreview = isset($previewRows);
@endphp

<div class="mb-8">
    <div class="flex items-start gap-4">
        <a href="{{ route('surat-masuk.index') }}" class="mt-1 inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Excel Import</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Import Surat Masuk</h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-500">Upload file Excel, cek hasil preview, lalu simpan hanya data yang valid. Format agenda lama dengan cell merge akan dirapikan otomatis.</p>
        </div>
    </div>
</div>

@unless($hasPreview)
<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
    <form action="{{ route('surat-masuk.import.store') }}" method="POST" enctype="multipart/form-data" data-turbo="false" data-loading-form data-loading-message="Membaca file Excel dan menyiapkan preview..." class="self-start overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        @csrf
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-950">Pilih File Excel</h2>
                    <p class="mt-1 text-sm text-slate-500">Data akan dibaca dulu sebagai preview sebelum disimpan.</p>
                </div>
                <span class="inline-flex w-fit rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100">.xlsx .xls .csv</span>
            </div>
        </div>

        <div class="p-6">
            <label for="file" class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/70 px-6 py-10 text-center transition hover:border-indigo-300 hover:bg-indigo-50/60">
                <span class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200 group-hover:ring-indigo-200">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5h10.5A2.25 2.25 0 0019.5 17.25V6.75A2.25 2.25 0 0017.25 4.5H6.75A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5z" />
                    </svg>
                </span>
                <span>
                    <span class="mt-4 block text-sm font-semibold text-slate-900">Klik untuk memilih file agenda surat masuk</span>
                    <span class="mt-1 block text-sm text-slate-500">Mendukung format kantor lama dengan cell merge dan template header aplikasi.</span>
                </span>
            </label>
            <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required class="sr-only">

            <div id="fileStatus" class="{{ $hasPreview ? '' : 'hidden' }} mt-3 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-indigo-600 ring-1 ring-indigo-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">File sudah dipilih</p>
                        <p id="selectedFileName" class="truncate text-sm font-semibold text-slate-900">{{ $fileName ?? '' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 hidden rounded-lg bg-indigo-50 px-4 py-3 text-sm text-indigo-800 ring-1 ring-indigo-100" data-loading-panel>
                <div class="flex items-center gap-3">
                    <span class="h-5 w-5 animate-spin rounded-full border-2 border-indigo-200 border-t-indigo-600"></span>
                    <span data-loading-text>Membaca file Excel dan menyiapkan preview...</span>
                </div>
            </div>

            <div class="mt-5 grid gap-3 border-t border-slate-100 pt-5 sm:grid-cols-3">
                <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                    <p class="text-xs font-semibold text-slate-900">1. Upload</p>
                    <p class="mt-1 text-xs text-slate-500">Pilih workbook agenda.</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                    <p class="text-xs font-semibold text-slate-900">2. Preview</p>
                    <p class="mt-1 text-xs text-slate-500">Cek valid, duplikat, error.</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-slate-100">
                    <p class="text-xs font-semibold text-slate-900">3. Simpan</p>
                    <p class="mt-1 text-xs text-slate-500">Hanya data valid masuk.</p>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <button type="submit" data-loading-button class="inline-flex justify-center rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-60">
                    Preview Data
                </button>
            </div>
        </div>
    </form>

    <aside class="self-start rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-sm font-bold text-slate-950">Yang dibaca sistem</h2>
        <div class="mt-4 space-y-3 text-sm">
            <div class="flex gap-3">
                <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                <p class="text-slate-600">Sheet dipilih otomatis dari header surat masuk, termasuk jika bernama Sheet1. Template datar dan agenda dengan cell merge didukung.</p>
            </div>
            <div class="flex gap-3">
                <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                <p class="text-slate-600">Cell merge akan diteruskan otomatis ke baris di bawahnya.</p>
            </div>
            <div class="flex gap-3">
                <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                <p class="text-slate-600">Data sama persis akan ditandai duplikat dan dilewati.</p>
            </div>
        </div>

        <div class="mt-5 rounded-lg bg-slate-50 p-4 ring-1 ring-slate-100">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Header template</p>
            <ul class="mt-3 grid gap-2 text-xs text-slate-600">
            <li><code>tanggal_masuk_surat</code></li>
            <li><code>nomor_urut</code></li>
            <li><code>alamat_pengirim</code></li>
            <li><code>tanggal_surat</code></li>
            <li><code>nomor_surat</code></li>
            <li><code>perihal</code></li>
            <li><code>tujuan_disposisi</code></li>
            </ul>
        </div>
    </aside>
</div>
@endunless

@isset($previewRows)
    <div class="{{ $hasPreview ? '' : 'mt-8' }} overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Preview</p>
                <h2 class="mt-1 text-lg font-bold text-slate-950">Hasil Baca File</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $fileName }}</p>
                <p class="mt-1 text-sm text-indigo-600">Sheet yang dibaca: <strong>{{ $selectedSheet ?? '—' }}</strong></p>
            </div>
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-lg bg-emerald-50 px-4 py-3 ring-1 ring-emerald-100">
                            <p class="text-xl font-bold text-emerald-700">{{ $validRows }}</p>
                            <p class="text-xs font-semibold text-emerald-700">Valid</p>
                        </div>
                        <div class="rounded-lg bg-amber-50 px-4 py-3 ring-1 ring-amber-100">
                            <p class="text-xl font-bold text-amber-700">{{ $duplicateRows }}</p>
                            <p class="text-xs font-semibold text-amber-700">Duplikat</p>
                        </div>
                        <div class="rounded-lg bg-red-50 px-4 py-3 ring-1 ring-red-100">
                            <p class="text-xl font-bold text-red-700">{{ $invalidRows }}</p>
                            <p class="text-xs font-semibold text-red-700">Error</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('surat-masuk.import') }}" class="inline-flex justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-300 hover:bg-slate-50">Upload Ulang</a>
                        <form action="{{ route('surat-masuk.import.store') }}" method="POST" data-turbo="false" data-loading-form data-loading-message="Menyimpan data valid ke database...">
                            @csrf
                            <input type="hidden" name="import_token" value="{{ $importToken }}">
                            <button type="submit" data-loading-button class="inline-flex w-full justify-center rounded-md bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-50" @disabled($validRows === 0)>
                                Simpan {{ $validRows }} Data Valid
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-h-[32rem] overflow-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="sticky top-0 z-10 bg-slate-50 shadow-sm">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Baris</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">Status</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">Tgl Masuk</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">No Urut</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">Pengirim</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">Tgl Surat</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">No Surat</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">Perihal</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">Tujuan</th>
                        <th class="px-3 py-3 text-left font-semibold text-slate-700">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($previewRows as $row)
                        @php
                            $badge = [
                                'valid' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                'duplicate' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                'error' => 'bg-red-50 text-red-700 ring-red-200',
                            ][$row['status']] ?? 'bg-slate-50 text-slate-700 ring-slate-200';
                        @endphp
                        <tr class="{{ $row['status'] === 'error' ? 'bg-red-50/30' : ($row['status'] === 'duplicate' ? 'bg-amber-50/30' : '') }}">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $row['row'] }}</td>
                            <td class="whitespace-nowrap px-3 py-3">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ring-1 {{ $badge }}">{{ ucfirst($row['status']) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $row['data']['tanggal_masuk_surat'] ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $row['data']['nomor_urut'] ?? '-' }}</td>
                            <td class="min-w-48 px-3 py-3 text-slate-700">{{ $row['data']['alamat_pengirim'] ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $row['data']['tanggal_surat'] ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $row['data']['nomor_surat'] ?? '-' }}</td>
                            <td class="min-w-72 px-3 py-3 text-slate-700">{{ $row['data']['perihal'] ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $row['data']['tujuan_disposisi'] ?? '-' }}</td>
                            <td class="min-w-56 px-3 py-3 text-slate-600">{{ $row['message'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 py-8 text-center text-sm italic text-slate-500">Tidak ada baris data yang terbaca dari file.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50/70 px-6 py-5 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('surat-masuk.import') }}" class="inline-flex justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-300 hover:bg-slate-50">Upload Ulang</a>
            <form action="{{ route('surat-masuk.import.store') }}" method="POST" data-turbo="false" data-loading-form data-loading-message="Menyimpan data valid ke database...">
                @csrf
                <input type="hidden" name="import_token" value="{{ $importToken }}">
                <button type="submit" data-loading-button class="inline-flex w-full justify-center rounded-md bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-50" @disabled($validRows === 0)>
                    Simpan {{ $validRows }} Data Valid
                </button>
            </form>
        </div>
    </div>
@endisset

@if(session('import_errors'))
    <div class="mt-6 rounded-lg bg-white p-5 shadow-sm ring-1 ring-amber-200">
        <h2 class="text-sm font-semibold text-amber-900">Baris yang dilewati</h2>
        <ul class="mt-3 list-disc pl-5 text-sm text-amber-800">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div id="importLoadingOverlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 px-4 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-xl">
        <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-indigo-100 border-t-indigo-600"></div>
        <h2 class="mt-4 text-base font-bold text-slate-900">Sedang diproses</h2>
        <p id="importLoadingMessage" class="mt-2 text-sm text-slate-500">Mohon tunggu, jangan klik ulang.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('file');
    const fileStatus = document.getElementById('fileStatus');
    const selectedFileName = document.getElementById('selectedFileName');

    if (fileInput && selectedFileName && fileStatus) {
        fileInput.addEventListener('change', () => {
            const fileName = fileInput.files?.[0]?.name;
            selectedFileName.textContent = fileName || '';
            fileStatus.classList.toggle('hidden', !fileName);
        });
    }

    document.querySelectorAll('[data-loading-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                return;
            }

            const message = form.dataset.loadingMessage || 'Sedang memproses data...';
            const overlay = document.getElementById('importLoadingOverlay');
            const overlayMessage = document.getElementById('importLoadingMessage');
            const panel = form.querySelector('[data-loading-panel]');
            const panelText = form.querySelector('[data-loading-text]');
            const button = form.querySelector('[data-loading-button]');

            if (overlay && overlayMessage) {
                overlayMessage.textContent = message;
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }

            if (panel) {
                panel.classList.remove('hidden');
            }
            if (panelText) {
                panelText.textContent = message;
            }
            if (button) {
                setTimeout(() => {
                    button.disabled = true;
                    button.textContent = 'Memproses...';
                }, 0);
            }
        });
    });
</script>
@endpush
