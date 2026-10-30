<?php

namespace App\Exports;

use App\Models\SuratKeluar;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuratKeluarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected $bulan,
        protected $tahun,
        protected $search = null
    ) {
    }

    public function collection()
    {
        $query = SuratKeluar::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('perihal', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_surat', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_urut', 'like', '%' . $this->search . '%')
                    ->orWhere('asal', 'like', '%' . $this->search . '%')
                    ->orWhere('alamat_penerima', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->bulan !== 'all') {
            $query->whereMonth('tanggal_keluar_surat', $this->bulan);
        }
        if ($this->tahun !== 'all') {
            $query->whereYear('tanggal_keluar_surat', $this->tahun);
        }

        return $query->orderBy('tanggal_keluar_surat')->get();
    }

    public function headings(): array
    {
        $namaBulan = [
            '01' => 'JANUARI', '02' => 'FEBRUARI', '03' => 'MARET', '04' => 'APRIL',
            '05' => 'MEI', '06' => 'JUNI', '07' => 'JULI', '08' => 'AGUSTUS',
            '09' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER',
        ];

        $judulBulan = $this->bulan === 'all' ? 'SEMUA BULAN' : ($namaBulan[str_pad((string) $this->bulan, 2, '0', STR_PAD_LEFT)] ?? '');
        $judulTahun = $this->tahun === 'all' ? 'SEMUA TAHUN' : $this->tahun;

        return [
            ['SURAT KELUAR BULAN '.$judulBulan.' TAHUN '.$judulTahun, '', '', '', '', '', ''],
            ['DINAS PETERNAKAN DAN KESEHATAN HEWAN PROVINSI NTB', '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            ['Tanggal Keluar Surat', 'Nomor Urut', 'Alamat Penerima', 'Dari Surat Keluar', '', '', 'Asal'],
            ['', '', '', 'Tanggal', 'Nomor', 'Perihal', ''],
        ];
    }

    public function map($surat): array
    {
        return [
            Carbon::parse($surat->tanggal_keluar_surat)->format('d/m/Y'),
            $surat->nomor_urut,
            $surat->alamat_penerima,
            Carbon::parse($surat->tanggal_surat)->format('d/m/Y'),
            $surat->nomor_surat,
            $surat->perihal,
            $surat->asal,
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

        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:F4');
        $sheet->mergeCells('G4:G5');
        $sheet->freezePane('A6');

        $sheet->getStyle('A1:G2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:G2')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A4:G5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF1A532'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 6) {
            $sheet->getStyle('A6:G'.$highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
        }

        foreach ([1, 2] as $row) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }
        $sheet->getRowDimension(4)->setRowHeight(25);
        $sheet->getRowDimension(5)->setRowHeight(20);

        return [];
    }

    public function title(): string
    {
        return 'Surat Keluar';
    }
}
