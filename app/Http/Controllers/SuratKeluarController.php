<?php

namespace App\Http\Controllers;

use App\Exports\SuratKeluarExport;
use App\Http\Requests\FilterSuratRequest;
use App\Http\Requests\SuratRequest;
use App\Models\SuratKeluar;
use App\Services\SuratService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SuratKeluarController extends Controller
{
    public function __construct(private SuratService $service) {}

    public function index(FilterSuratRequest $request)
    {
        $query = SuratKeluar::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('perihal', 'like', '%'.$request->search.'%')
                    ->orWhere('nomor_surat', 'like', '%'.$request->search.'%')
                    ->orWhere('nomor_urut', 'like', '%'.$request->search.'%')
                    ->orWhere('asal', 'like', '%'.$request->search.'%')
                    ->orWhere('alamat_penerima', 'like', '%'.$request->search.'%');
            });
        }

        $filterBulan = $request->input('bulan') ?: date('m');
        $filterTahun = $request->input('tahun') ?: date('Y');

        if ($filterBulan !== 'all') {
            $query->whereMonth('tanggal_keluar_surat', $filterBulan);
        }
        if ($filterTahun !== 'all') {
            $query->whereYear('tanggal_keluar_surat', $filterTahun);
        }

        $surat_keluar = $query->orderBy('tanggal_keluar_surat', 'desc')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('surat_keluar.index', compact('surat_keluar', 'filterBulan', 'filterTahun'));
    }

    public function create()
    {
        $nomorRekomendasi = $this->rekomendasiNomorAgenda();

        return view('surat_keluar.create', compact('nomorRekomendasi'));
    }

    public function store(SuratRequest $request)
    {
        $this->service->save(SuratKeluar::class, $request->safe()->except(['lampiran', 'hapus_lampiran']), $request->user()->id, file: $request->file('lampiran'));

        return redirect()->route('surat-keluar.index')->with('success', 'Data surat keluar berhasil ditambahkan.');
    }

    public function show(SuratKeluar $surat_keluar)
    {
        $surat_keluar->load(['pembuat', 'pengubahTerakhir', 'suratMasukAsal']);

        return view('surat_keluar.show', compact('surat_keluar'));
    }

    public function edit(SuratKeluar $surat_keluar)
    {
        return view('surat_keluar.edit', compact('surat_keluar'));
    }

    public function update(SuratRequest $request, SuratKeluar $surat_keluar)
    {
        $this->service->save(SuratKeluar::class, $request->safe()->except(['lampiran', 'hapus_lampiran']), $request->user()->id,
            $surat_keluar, $request->file('lampiran'), $request->boolean('hapus_lampiran'));

        return redirect()->route('surat-keluar.index')->with('success', 'Data surat keluar berhasil diperbarui.');
    }

    public function destroy(SuratKeluar $surat_keluar)
    {
        Gate::authorize('manage-surat');
        $this->service->delete($surat_keluar);

        return redirect()->route('surat-keluar.index')->with('success', 'Data surat keluar berhasil dihapus.');
    }

    public function reset(Request $request)
    {
        Gate::authorize('manage-surat');
        $request->validate(['confirmation' => ['required', 'in:HAPUS']]);
        $this->service->reset(SuratKeluar::class);

        return redirect()->route('surat-keluar.index')->with('success', 'Data surat keluar berhasil dikosongkan.');
    }

    public function export(FilterSuratRequest $request)
    {
        $bulan = $request->input('bulan') ?: date('m');
        $tahun = $request->input('tahun') ?: date('Y');
        $search = $request->input('search');
        $namaFile = 'Agenda_Surat_Keluar_'.$this->labelPeriode($bulan, $tahun).'.xlsx';

        return Excel::download(new SuratKeluarExport($bulan, $tahun, $search), $namaFile);
    }

    private function rekomendasiNomorAgenda(): string
    {
        $bulan = date('m');
        $tahun = date('Y');

        $last = SuratKeluar::whereYear('tanggal_keluar_surat', $tahun)
            ->whereMonth('tanggal_keluar_surat', $bulan)
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
        return Str::slug(($bulan === 'all' ? 'Semua_Bulan' : str_pad((string) $bulan, 2, '0', STR_PAD_LEFT)).'_'.($tahun === 'all' ? 'Semua_Tahun' : $tahun), '_');
    }
}
