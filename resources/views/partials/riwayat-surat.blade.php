<section class="mt-6 rounded-2xl bg-white p-5 sm:p-6 shadow-sm ring-1 ring-slate-100">
    <h2 class="text-lg font-bold text-slate-800">Informasi Pencatatan</h2>
    <dl class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-sm text-slate-500">Dicatat oleh</dt>
            <dd class="mt-1 font-semibold text-slate-800">{{ $surat->pembuat?->name ?? 'Tidak tercatat' }}
                @if($surat->pembuat?->trashed())<span class="text-xs font-normal text-slate-500">(akun dihapus)</span>@endif
            </dd>
            <dd class="text-xs text-slate-500">{{ $surat->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-sm text-slate-500">Terakhir diperbarui oleh</dt>
            <dd class="mt-1 font-semibold text-slate-800">{{ $surat->pengubahTerakhir?->name ?? 'Belum diperbarui' }}
                @if($surat->pengubahTerakhir?->trashed())<span class="text-xs font-normal text-slate-500">(akun dihapus)</span>@endif
            </dd>
            @if($surat->updated_by)<dd class="text-xs text-slate-500">{{ $surat->updated_at?->format('d/m/Y H:i') }}</dd>@endif
        </div>
    </dl>
</section>
