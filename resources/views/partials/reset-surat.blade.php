@can('manage-surat')
<details class="mt-6 rounded-xl border border-rose-100 bg-white p-4">
    <summary class="cursor-pointer text-sm font-semibold text-rose-700">Kosongkan seluruh data surat {{ $jenis }}</summary>
    <p class="mt-3 text-sm text-slate-600">Tindakan ini menghapus seluruh data surat {{ $jenis }} beserta lampirannya, termasuk data di luar filter yang sedang ditampilkan.</p>
    <form action="{{ route('surat-'.$jenis.'.reset') }}" method="POST" class="mt-3 flex flex-wrap items-end gap-3" onsubmit="return confirm('Hapus seluruh surat {{ $jenis }} beserta lampirannya?')">
        @csrf
        @method('DELETE')
        <div>
            <label class="block text-xs text-slate-600" for="reset-{{ $jenis }}">Ketik HAPUS untuk melanjutkan</label>
            <input id="reset-{{ $jenis }}" name="confirmation" required pattern="HAPUS" autocomplete="off" class="mt-1 rounded-lg border-slate-200 text-sm">
        </div>
        <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">Hapus seluruh data</button>
    </form>
</details>
@endcan
