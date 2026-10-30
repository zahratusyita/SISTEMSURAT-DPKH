@php
    $selectedId = old('surat_masuk_id', $surat_keluar->surat_masuk_id ?? null);
    $selectedSurat = is_scalar($selectedId) && ctype_digit((string) $selectedId) ? \App\Models\SuratMasuk::find($selectedId) : null;
@endphp
<section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100" data-reply-picker>
    <h2 class="text-lg font-bold text-slate-800">Hubungan Surat Balasan</h2>
    <p class="mt-1 text-sm text-slate-500">Pilih surat masuk yang dibalas. Kosongkan jika surat keluar ini bukan balasan.</p>
    <label for="reply-search" class="mt-4 block text-sm font-semibold text-slate-700">Cari surat masuk</label>
    <input id="reply-search" type="search" autocomplete="off" placeholder="Nomor, perihal, atau pengirim" class="mt-1 w-full rounded-xl border-slate-200 text-sm">
    <label for="surat_masuk_id" class="mt-3 block text-sm font-semibold text-slate-700">Balasan untuk surat masuk</label>
    <select id="surat_masuk_id" name="surat_masuk_id" class="mt-1 w-full rounded-xl border-slate-200 text-sm">
        <option value="">Bukan surat balasan</option>
        @if($selectedSurat)<option value="{{ $selectedSurat->id }}" selected>{{ $selectedSurat->nomor_surat ?: 'Tanpa nomor' }} - {{ $selectedSurat->perihal }}</option>@endif
    </select>
    <p class="mt-2 text-xs text-slate-500" data-reply-status aria-live="polite">Menampilkan maksimal 20 hasil pencarian.</p>
    @error('surat_masuk_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
</section>
@push('scripts')
<script>
(() => {
    const picker = document.querySelector('[data-reply-picker]');
    if (!picker) return;
    const input = picker.querySelector('#reply-search');
    const select = picker.querySelector('#surat_masuk_id');
    const status = picker.querySelector('[data-reply-status]');
    let timer;
    let controller;
    async function search() {
        controller?.abort();
        controller = new AbortController();
        status.textContent = 'Mencari surat masuk...';
        try {
            const url = new URL(@json(route('surat-masuk.lookup')));
            url.searchParams.set('q', input.value);
            const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: controller.signal });
            if (!response.ok) throw new Error('Pencarian gagal');
            const results = await response.json();
            const current = select.value;
            const currentLabel = select.selectedOptions[0]?.textContent;
            select.replaceChildren(new Option('Bukan surat balasan', ''));
            if (current && !results.some(row => String(row.id) === current)) {
                select.add(new Option(currentLabel, current));
            }
            for (const row of results) {
                select.add(new Option(`${row.nomor_surat || 'Tanpa nomor'} - ${row.perihal} (${row.alamat_pengirim || 'Pengirim tidak tercatat'})`, row.id));
            }
            select.value = current;
            status.textContent = results.length ? `${results.length} hasil ditampilkan. Pilihan surat yang sudah dipilih tetap tersimpan.` : 'Tidak ada surat masuk yang cocok.';
        } catch (error) {
            if (error.name !== 'AbortError') status.textContent = 'Pencarian tidak berhasil. Coba kembali.';
        }
    }
    input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(search, 300); });
    search();
})();
</script>
@endpush
