<?php

namespace App\Http\Controllers;

use App\Exports\SuratMasukExport;
use App\Http\Requests\FilterSuratRequest;
use App\Http\Requests\SuratRequest;
use App\Models\SuratMasuk;
use App\Services\SuratService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SuratMasukController extends Controller
{
    public function __construct(private SuratService $service) {}

    public function index(FilterSuratRequest $request)
    {
        $query = SuratMasuk::query();

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('perihal', 'like', '%'.$request->search.'%')
                    ->orWhere('nomor_surat', 'like', '%'.$request->search.'%')
                    ->orWhere('nomor_urut', 'like', '%'.$request->search.'%')
                    ->orWhere('tujuan_disposisi', 'like', '%'.$request->search.'%')
                    ->orWhere('alamat_pengirim', 'like', '%'.$request->search.'%');
            });
        }

        $filterBulan = $request->input('bulan') ?: date('m');
        $filterTahun = $request->input('tahun') ?: date('Y');

        if ($filterBulan != 'all') {
            $query->whereMonth('tanggal_masuk_surat', $filterBulan);
        }
        if ($filterTahun != 'all') {
            $query->whereYear('tanggal_masuk_surat', $filterTahun);
        }

        $surat_masuk = $query->orderBy('tanggal_masuk_surat', 'desc')->paginate(10)->withQueryString();

        return view('surat_masuk.index', compact('surat_masuk', 'filterBulan', 'filterTahun'));
    }

    public function create()
    {
        $nomorRekomendasi = $this->rekomendasiNomorAgenda();

        return view('surat_masuk.create', compact('nomorRekomendasi'));
    }

    public function store(SuratRequest $request)
    {
        $this->service->save(SuratMasuk::class, $request->safe()->except(['lampiran', 'hapus_lampiran']), $request->user()->id, file: $request->file('lampiran'));

        return redirect()->route('surat-masuk.index')->with('success', 'Data surat masuk berhasil ditambahkan.');
    }

    public function show(SuratMasuk $surat_masuk)
    {
        $surat_masuk->load(['pembuat', 'pengubahTerakhir']);
        $balasan = $surat_masuk->balasan()->latest('tanggal_keluar_surat')->paginate(10);

        return view('surat_masuk.show', compact('surat_masuk', 'balasan'));
    }

    public function edit(SuratMasuk $surat_masuk)
    {
        return view('surat_masuk.edit', compact('surat_masuk'));
    }

    public function update(SuratRequest $request, SuratMasuk $surat_masuk)
    {
        $this->service->save(SuratMasuk::class, $request->safe()->except(['lampiran', 'hapus_lampiran']), $request->user()->id,
            $surat_masuk, $request->file('lampiran'), $request->boolean('hapus_lampiran'));

        return redirect()->route('surat-masuk.index')->with('success', 'Data surat masuk berhasil diperbarui.');
    }

    public function destroy(SuratMasuk $surat_masuk)
    {
        Gate::authorize('manage-surat');
        $this->service->delete($surat_masuk);

        return redirect()->route('surat-masuk.index')->with('success', 'Data surat masuk berhasil dihapus.');
    }

    public function reset(Request $request)
    {
        Gate::authorize('manage-surat');
        $request->validate(['confirmation' => ['required', 'in:HAPUS']]);
        $this->service->reset(SuratMasuk::class);

        return redirect()->route('surat-masuk.index')->with('success', 'Data surat masuk berhasil dikosongkan.');
    }

    public function export(FilterSuratRequest $request)
    {
        $bulan = $request->input('bulan') ?: date('m');
        $tahun = $request->input('tahun') ?: date('Y');
        $search = $request->input('search');
        $namaFile = 'Agenda_Surat_Masuk_'.$this->labelPeriode($bulan, $tahun).'.xlsx';

        return Excel::download(new SuratMasukExport($bulan, $tahun, $search), $namaFile);
    }

    private function rekomendasiNomorAgenda(): string
    {
        $bulan = date('m');
        $tahun = date('Y');

        $last = SuratMasuk::whereYear('tanggal_masuk_surat', $tahun)
            ->whereMonth('tanggal_masuk_surat', $bulan)
            ->orderByDesc('id')
            ->pluck('nomor_urut')
            ->first(fn ($nomor) => preg_match('/^\d+/', (string) $nomor));

        $next = 1;
        if ($last && preg_match('/^(\d+)/', $last, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return str_pad((string) $next, 3, '0', STR_PAD_LEFT).'/'.$this->romawi((int) $bulan).'/'.$tahun;
    }

    private function romawi(int $bulan): string
    {
        return [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][$bulan] ?? (string) $bulan;
    }

    private function labelPeriode($bulan, $tahun): string
    {
        $parts = [];
        $parts[] = $bulan === 'all' ? 'Semua_Bulan' : str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);
        $parts[] = $tahun === 'all' ? 'Semua_Tahun' : (string) $tahun;

        return Str::slug(implode('_', $parts), '_');
    }
}
