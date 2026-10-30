<?php

namespace Tests\Feature;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class MultiSheetImportTest extends TestCase
{
    use RefreshDatabase;

    private const MASUK = ['tanggal_masuk_surat', 'nomor_urut', 'alamat_pengirim', 'tanggal_surat', 'nomor_surat', 'perihal', 'tujuan_disposisi'];

    private const KELUAR = ['tanggal_keluar_surat', 'nomor_urut', 'alamat_penerima', 'tanggal_surat', 'nomor_surat', 'perihal', 'asal'];

    private function workbook(array $definitions): UploadedFile
    {
        $book = new Spreadsheet;
        foreach ($definitions as $index => $definition) {
            $sheet = $index === 0 ? $book->getActiveSheet() : $book->createSheet();
            $sheet->setTitle($definition['name']);
            $sheet->fromArray($definition['rows'], null, 'A1');
            foreach ($definition['merge'] ?? [] as $range) {
                $sheet->mergeCells($range);
            }
        }
        ob_start();
        try {
            (new Xlsx($book))->save('php://output');
            $bytes = ob_get_contents();
        } finally {
            ob_end_clean();
            $book->disconnectWorksheets();
        }

        return UploadedFile::fake()->createWithContent('agenda-dua-sheet.xlsx', $bytes);
    }

    private function login(): User
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_same_workbook_imports_incoming_sheet1_and_outgoing_sheet2(): void
    {
        $user = $this->login();
        $definitions = [
            ['name' => 'Sheet1', 'rows' => [self::MASUK, ['2026-09-16', 'IN/001', 'Instansi A', '2026-09-15', 'IN-001', 'Permohonan masuk', 'Umum']]],
            ['name' => 'Sheet2', 'rows' => [self::KELUAR, ['2026-09-17', 'OUT/001', 'Instansi B', '2026-09-17', 'OUT-001', 'Jawaban keluar', 'Sekretariat']]],
        ];
        foreach (['masuk' => 'Sheet1', 'keluar' => 'Sheet2'] as $kind => $sheet) {
            $this->post(route('surat-'.$kind.'.import.store'), ['file' => $this->workbook($definitions)])
                ->assertOk()->assertViewHas('validRows', 1)->assertViewHas('invalidRows', 0)->assertViewHas('selectedSheet', $sheet);
            $this->post(route('surat-'.$kind.'.import.store'), ['import_token' => session('surat_'.$kind.'_import.token')])
                ->assertRedirect(route('surat-'.$kind.'.index'));
        }
        $this->assertDatabaseCount('surat_masuk', 1);
        $this->assertDatabaseCount('surat_keluar', 1);
        $this->assertSame('Permohonan masuk', SuratMasuk::first()->perihal);
        $this->assertSame('Jawaban keluar', SuratKeluar::first()->perihal);
        $this->assertSame('2026-09-17', SuratKeluar::first()->tanggal_keluar_surat);
        $this->assertSame($user->id, SuratKeluar::first()->created_by);
    }

    public function test_sheet2_legacy_agenda_handles_multiline_headers_and_merged_dates(): void
    {
        $user = $this->login();
        $file = $this->workbook([
            ['name' => 'Sheet1', 'rows' => [self::MASUK, ['2026-09-16', 'IN/1', 'A', '2026-09-16', 'IN-1', 'Masuk', 'Umum']]],
            ['name' => 'Sheet2', 'rows' => [
                [], [], [], [],
                [null, "Tanggal\nKeluar Surat", "Nomor\nUrut", "Alamat\nPenerima", 'Dari Surat Keluar', null, null, 'Asal'],
                [null, null, null, null, 'Tanggal', 'Nomor', 'Perihal', null],
                [null, '17/09/2026', 'OUT/1', 'B', '17/09/2026', 'OUT-1', 'Balasan pertama', 'Umum'],
                [null, null, 'OUT/2', null, null, 'OUT-2', 'Balasan kedua', null],
            ], 'merge' => ['B7:B8', 'D7:D8', 'E7:E8', 'H7:H8']],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()
            ->assertViewHas('selectedSheet', 'Sheet2')->assertViewHas('validRows', 2)->assertViewHas('invalidRows', 0);
        $this->post(route('surat-keluar.import.store'), ['import_token' => session('surat_keluar_import.token')])
            ->assertRedirect(route('surat-keluar.index'));
        $this->assertDatabaseCount('surat_masuk', 0);
        $this->assertDatabaseCount('surat_keluar', 2);
        foreach (SuratKeluar::all() as $letter) {
            $this->assertSame('2026-09-17', $letter->tanggal_keluar_surat);
            $this->assertSame('2026-09-17', $letter->tanggal_surat);
            $this->assertSame($user->id, $letter->created_by);
        }
    }

    public function test_legacy_blank_outgoing_dates_use_letter_dates_and_skip_three_row_header(): void
    {
        $this->login();
        $file = $this->workbook([
            ['name' => 'Surat Keluar', 'rows' => [
                ['SURAT KELUAR FEBRUARI 2026'], [], [],
                [null, 'Tanggal Keluar Surat', 'Nomor Urut', 'Alamat Penerima', 'Dari Surat Keluar', null, null, 'Asal'],
                [],
                [null, null, null, null, 'Tanggal', 'Nomor', 'Perihal', null],
                [null, null, '138', 'A', '02/02/2026', '-', 'Nota dinas', 'Umum'],
                [null, null, '138.a', 'B', '03/02/2026', '-', 'Permintaan dokumen', 'Umum'],
            ], 'merge' => ['B4:B6', 'C4:C6', 'D4:D6', 'E4:G5', 'H4:H6']],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()
            ->assertViewHas('validRows', 2)->assertViewHas('invalidRows', 0)
            ->assertViewHas('previewRows', function ($rows) {
                return array_column($rows, 'row') === [7, 8]
                    && $rows[0]['data']['tanggal_keluar_surat'] === '2026-02-02'
                    && $rows[1]['data']['tanggal_keluar_surat'] === '2026-02-03'
                    && str_contains($rows[0]['message'], 'Tanggal keluar mengikuti tanggal surat');
            });
        $this->post(route('surat-keluar.import.store'), ['import_token' => session('surat_keluar_import.token')])
            ->assertRedirect(route('surat-keluar.index'));
        $this->assertDatabaseCount('surat_keluar', 2);
        foreach (SuratKeluar::all() as $letter) {
            $this->assertSame($letter->tanggal_surat, $letter->tanggal_keluar_surat);
        }
    }

    public function test_explicit_invalid_legacy_outgoing_date_is_not_replaced(): void
    {
        $this->login();
        $file = $this->workbook([
            ['name' => 'Sheet2', 'rows' => [
                [null, 'Tanggal Keluar Surat', 'Nomor Urut', 'Alamat Penerima', 'Dari Surat Keluar', null, null, 'Asal'],
                [null, null, null, null, 'Tanggal', 'Nomor', 'Perihal', null],
                [null, 'tanggal salah', 'OUT/1', 'A', '2026-02-02', '-', 'Nota dinas', 'Umum'],
            ]],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()
            ->assertViewHas('validRows', 0)->assertViewHas('invalidRows', 1);
        $this->assertDatabaseCount('surat_keluar', 0);
    }

    public function test_legacy_missing_both_dates_is_not_accepted(): void
    {
        $this->login();
        $file = $this->workbook([
            ['name' => 'Sheet2', 'rows' => [
                [null, 'Tanggal Keluar Surat', 'Nomor Urut', 'Alamat Penerima', 'Dari Surat Keluar', null, null, 'Asal'],
                [null, null, null, null, 'Tanggal', 'Nomor', 'Perihal', null],
                [null, null, 'OUT/1', 'A', null, '-', 'Nota dinas', 'Umum'],
            ]],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()
            ->assertViewHas('validRows', 0)->assertViewHas('invalidRows', 1);
        $this->assertDatabaseCount('surat_keluar', 0);
    }

    public function test_matching_named_sheet_is_preferred_case_insensitively(): void
    {
        $this->login();
        $file = $this->workbook([
            ['name' => 'Sheet1', 'rows' => [self::KELUAR, ['2026-09-16', 'OUT/1', 'A', '2026-09-16', 'OUT-1', 'Sheet generik', 'Umum']]],
            ['name' => 'sURAT_Keluar', 'rows' => [self::KELUAR, ['2026-09-17', 'OUT/2', 'B', '2026-09-17', 'OUT-2', 'Sheet bernama', 'Umum']]],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()
            ->assertViewHas('selectedSheet', 'sURAT_Keluar')->assertViewHas('validRows', 1)
            ->assertViewHas('previewRows', fn ($rows) => $rows[0]['data']['perihal'] === 'Sheet bernama');
    }

    public function test_sheet_name_does_not_override_wrong_letter_headers(): void
    {
        $this->login();
        $file = $this->workbook([
            ['name' => 'Surat Keluar', 'rows' => [self::MASUK, ['2026-09-16', 'IN/1', 'A', '2026-09-16', 'IN-1', 'Masuk', 'Umum']]],
            ['name' => 'Sheet2', 'rows' => [self::KELUAR, ['2026-09-17', 'OUT/2', 'B', '2026-09-17', 'OUT-2', 'Keluar', 'Umum']]],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()
            ->assertViewHas('selectedSheet', 'Sheet2')->assertViewHas('validRows', 1);
    }

    public function test_missing_outgoing_sheet_is_reported_without_reading_incoming_rows(): void
    {
        $this->login();
        $file = $this->workbook([
            ['name' => 'Sheet1', 'rows' => [self::MASUK, ['2026-09-16', 'IN/1', 'A', '2026-09-16', 'IN-1', 'Masuk', 'Umum']]],
            ['name' => 'Sheet2', 'rows' => []],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertSessionHasErrors('file');
        $this->assertStringContainsString('Tidak ditemukan sheet Surat Keluar', session('errors')->first('file'));
        $this->assertDatabaseCount('surat_keluar', 0);
    }

    public function test_a_truly_missing_outgoing_date_still_requires_validation(): void
    {
        $this->login();
        $file = $this->workbook([
            ['name' => 'Sheet2', 'rows' => [self::KELUAR, [null, 'OUT/1', 'A', '2026-09-16', 'OUT-1', 'Keluar', 'Umum']]],
        ]);
        $this->post(route('surat-keluar.import.store'), ['file' => $file])->assertOk()
            ->assertViewHas('selectedSheet', 'Sheet2')->assertViewHas('validRows', 0)->assertViewHas('invalidRows', 1)
            ->assertViewHas('previewRows', fn ($rows) => $rows[0]['message'] === 'Tanggal keluar surat wajib diisi.');
        $this->assertDatabaseCount('surat_keluar', 0);
    }
}
