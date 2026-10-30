<?php

namespace App\Exports;

use App\Models\SuratMasuk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Carbon\Carbon;

class SuratMasukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;
    protected $search;

    public function __construct($bulan, $tahun, $search = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->search = $search;
    }

    public function collection()
    {
        $query = SuratMasuk::query();
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('perihal', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_surat', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_urut', 'like', '%' . $this->search . '%')
                  ->orWhere('tujuan_disposisi', 'like', '%' . $this->search . '%')
                  ->orWhere('alamat_pengirim', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->bulan != 'all') {
            $query->whereMonth('tanggal_masuk_surat', $this->bulan);
        }
        if ($this->tahun != 'all') {
            $query->whereYear('tanggal_masuk_surat', $this->tahun);
        }
        return $query->orderBy('tanggal_masuk_surat', 'asc')->get();
    }

    public function map($surat): array
    {
        return [
            Carbon::parse($surat->tanggal_masuk_surat)->format('d/m/Y'),
            $surat->nomor_urut,
            $surat->alamat_pengirim,
            Carbon::parse($surat->tanggal_surat)->format('d/m/Y'),
            $surat->nomor_surat,
            $surat->perihal,
            $surat->tujuan_disposisi,
        ];
    }

    public function headings(): array
    {
        $namaBulan = ['01'=>'JANUARI','02'=>'FEBRUARI','03'=>'MARET','04'=>'APRIL','05'=>'MEI','06'=>'JUNI',
                      '07'=>'JULI','08'=>'AGUSTUS','09'=>'SEPTEMBER','10'=>'OKTOBER','11'=>'NOVEMBER','12'=>'DESEMBER'];
        $judulBulan = $this->bulan == 'all' ? 'SEMUA BULAN' : ($namaBulan[str_pad($this->bulan, 2, '0', STR_PAD_LEFT)] ?? '');
        $judulTahun = $this->tahun == 'all' ? 'SEMUA TAHUN' : $this->tahun;

        return [
            ['SURAT MASUK BULAN ' . $judulBulan . ' TAHUN ' . $judulTahun, '', '', '', '', '', ''],
            ['DINAS PETERNAKAN DAN KESEHATAN HEWAN PROVINSI NTB', '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''], // Baris kosong pemisah
            ['Tanggal Masuk Surat', 'Nomor Urut', 'Alamat Pengirim', 'Dari Surat Masuk', '', '', 'Tujuan/Disposisi'],
            ['', '', '', 'Tanggal', 'Nomor', 'Perihal', '']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setTitle($this->title());
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        // ------------------
        // Style untuk Judul Konstan (Baris 1 & 2)
        // ------------------
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        
        $sheet->getStyle('A1:G2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Garis batas bawah untuk Judul Dinas (seperti di gambar)
        $sheet->getStyle('A2:G2')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['argb' => 'FF000000']
                ],
            ],
        ]);

        // ------------------
        // Style untuk Header Tabel (Baris 4 & 5)
        // ------------------
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:F4');
        $sheet->mergeCells('G4:G5');
        $sheet->freezePane('A6');

        $sheet->getStyle('A4:G5')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF9FC5E8', // Biru muda Excel standar
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // ------------------
        // Style untuk Seluruh Data Body (Mulai Baris 6 ke bawah)
        // ------------------
        $highestRow = $sheet->getHighestRow();
        
        // Memastikan agar border data ditarik jika ada setidaknya 1 row data (mulai row 6)
        if ($highestRow >= 6) {
            $sheet->getStyle('A6:G' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]
            ]);
        }

        // Opsional: Tinggi baris biar lebih renggang dan rapi
        $sheet->getRowDimension('1')->setRowHeight(20);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('4')->setRowHeight(25);
        $sheet->getRowDimension('5')->setRowHeight(20);

        return [];
    }

    public function title(): string
    {
        return 'Surat Masuk';
    }
}
