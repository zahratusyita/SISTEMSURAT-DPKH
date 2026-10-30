<?php

namespace App\Imports;

use App\Http\Requests\SuratRequest;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

abstract class AgendaImport implements ToCollection, WithEvents
{
    public int $created = 0;

    public int $validRows = 0;

    public int $duplicateRows = 0;

    public int $invalidRows = 0;

    public array $errors = [];

    public array $previewRows = [];

    private array $seen = [];

    public ?string $selectedSheet = null;

    private bool $acceptSheet = true;

    public function __construct(public bool $previewOnly = false, private ?int $actorId = null) {}

    abstract protected function keluar(): bool;

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $expected = $this->keluar() ? 'Surat Keluar' : 'Surat Masuk';
                $matches = [];
                foreach ($event->reader->getDelegate()->getWorksheetIterator() as $sheet) {
                    $column = Coordinate::stringFromColumnIndex(min(64, Coordinate::columnIndexFromString($sheet->getHighestDataColumn())));
                    $lastRow = min(20, $sheet->getHighestDataRow());
                    $rows = collect($sheet->rangeToArray('A1:'.$column.$lastRow, null, false, false))->map(fn ($row) => collect($row));
                    if ($this->findHeader($rows) !== null) {
                        $matches[] = $sheet->getTitle();
                    }
                }
                $this->selectedSheet = collect($matches)->first(fn ($name) => $this->header($name) === $this->header($expected)) ?? ($matches[0] ?? null);
                if ($this->selectedSheet === null) {
                    throw ValidationException::withMessages(['file' => 'Tidak ditemukan sheet '.$expected.' dengan header yang sesuai. Pastikan terdapat kolom Tanggal '.($this->keluar() ? 'Keluar' : 'Masuk').' Surat dan kolom surat lainnya.']);
                }
            },
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->acceptSheet = $event->sheet->getDelegate()->getTitle() === $this->selectedSheet;
            },
        ];
    }

    private function aliases(): array
    {
        $dateField = $this->keluar() ? 'tanggal_keluar_surat' : 'tanggal_masuk_surat';
        $partyField = $this->keluar() ? 'alamat_penerima' : 'alamat_pengirim';
        $unitField = $this->keluar() ? 'asal' : 'tujuan_disposisi';
        $aliases = [
            $dateField => [$dateField, $this->keluar() ? 'tanggal_keluar' : 'tanggal_masuk', $this->keluar() ? 'tgl_keluar' : 'tgl_masuk', $this->keluar() ? 'tgl_keluar_surat' : 'tgl_masuk_surat'],
            'nomor_urut' => ['nomor_urut', 'no_urut', 'nomor_agenda', 'no_agenda'],
            $partyField => [$partyField, $this->keluar() ? 'penerima' : 'pengirim', 'asal_surat'],
            'tanggal_surat' => ['tanggal_surat', 'tgl_surat'],
            'nomor_surat' => ['nomor_surat', 'no_surat'],
            'perihal' => ['perihal', 'hal'],
            $unitField => [$unitField, $this->keluar() ? 'asal_surat' : 'disposisi', 'tujuan'],
        ];
        if ($this->keluar()) {
            $aliases['surat_masuk_id'] = ['surat_masuk_id'];
        } else {
            $aliases[$partyField][] = 'asal';
        }

        return $aliases;
    }

    private function findHeader(Collection $rows): ?array
    {
        $aliases = $this->aliases();
        $dateField = $this->keluar() ? 'tanggal_keluar_surat' : 'tanggal_masuk_surat';
        $partyField = $this->keluar() ? 'alamat_penerima' : 'alamat_pengirim';
        foreach ($rows->take(20) as $index => $row) {
            $headers = $row->map(fn ($v) => $this->header($v))->all();
            if (array_intersect($headers, $aliases['perihal']) && array_intersect($headers, $aliases['tanggal_surat']) && array_intersect($headers, $aliases[$dateField])) {
                return ['kind' => 'flat', 'index' => $index, 'headers' => $headers];
            }
        }
        foreach ($rows->take(20) as $index => $row) {
            $headers = $row->map(fn ($v) => $this->header($v))->all();
            $agendaColumn = false;
            foreach ($aliases['nomor_urut'] as $alias) {
                $agendaColumn = array_search($alias, $headers, true);
                if ($agendaColumn !== false) {
                    break;
                }
            }
            if ($agendaColumn !== false && $agendaColumn >= 1 && array_intersect($headers, $aliases[$partyField]) && array_intersect($headers, $aliases[$dateField])) {
                $offset = $agendaColumn - 1;

                return ['kind' => 'agenda', 'index' => $index, 'offset' => $offset, 'start' => $this->agendaDataStart($rows, $index + 1, $offset)];
            }
        }

        return null;
    }

    private function agendaDataStart(Collection $rows, int $start, int $offset): int
    {
        while ($start < $rows->count()) {
            $row = $rows->get($start);
            $values = [];
            for ($column = 0; $column < 7; $column++) {
                $values[] = $this->cell($row, $offset + $column);
            }
            $blank = array_filter($values, fn ($value) => $value !== null) === [];
            $subheader = $values[0] === null && $values[1] === null
                && in_array($this->header($values[3]), ['tanggal', 'tanggal_surat', 'tgl_surat'], true)
                && in_array($this->header($values[4]), ['nomor', 'nomor_surat', 'no_surat'], true)
                && in_array($this->header($values[5]), ['perihal', 'hal'], true);
            if (! $blank && ! $subheader) {
                break;
            }
            $start++;
        }

        return $start;
    }

    public function collection(Collection $rows): void
    {
        if (! $this->acceptSheet) {
            return;
        }
        $layout = $this->findHeader($rows);
        if ($layout === null) {
            throw ValidationException::withMessages(['file' => 'Format tidak dikenali. Gunakan template dengan header kolom atau berkas agenda hasil ekspor sistem.']);
        }
        $dateField = $this->keluar() ? 'tanggal_keluar_surat' : 'tanggal_masuk_surat';
        $partyField = $this->keluar() ? 'alamat_penerima' : 'alamat_pengirim';
        $unitField = $this->keluar() ? 'asal' : 'tujuan_disposisi';
        $aliases = $this->aliases();
        if ($layout['kind'] === 'flat') {
            $headers = $layout['headers'];
            foreach ($rows->slice($layout['index'] + 1) as $rowIndex => $data) {
                $payload = [];
                foreach ($aliases as $field => $names) {
                    $value = null;
                    foreach ($names as $name) {
                        $column = array_search($name, $headers, true);
                        if ($column !== false) {
                            $value = $this->cell($data, $column);
                            break;
                        }
                    }
                    $payload[$field] = str_starts_with($field, 'tanggal_') ? $this->dateValue($value) : $value;
                }
                if (array_filter($payload, fn ($value) => $value !== null) !== []) {
                    $this->createRow($payload, $rowIndex + 1);
                }
            }

            return;
        }
        // Exported seven-column agendas and legacy agendas with a leading column.
        $offset = $layout['offset'];
        $start = $layout['start'];
        $last = [];
        foreach ($rows->slice($start) as $rowIndex => $data) {
            $raw = [];
            foreach ([$dateField, 'nomor_urut', $partyField, 'tanggal_surat', 'nomor_surat', 'perihal', $unitField] as $column => $field) {
                $raw[$field] = $this->cell($data, $offset + $column);
            }
            if (array_filter($raw, fn ($value) => $value !== null) === []) {
                continue;
            }
            foreach ($raw as $field => $value) {
                if ($field !== 'nomor_urut' && $value !== null) {
                    $last[$field] = str_starts_with($field, 'tanggal_') ? $this->dateValue($value) : $value;
                }
            }
            $payload = $last + array_fill_keys(array_keys($raw), null);
            $payload['nomor_urut'] = $raw['nomor_urut'];
            if ($raw['tanggal_surat'] === '-') {
                $payload['tanggal_surat'] = $payload[$dateField];
            }
            if ($this->keluar()) {
                $payload['surat_masuk_id'] = null;
            }
            $note = null;
            if ($this->keluar() && $raw[$dateField] === null && $payload[$dateField] === null && $payload['tanggal_surat'] !== null) {
                $payload[$dateField] = $payload['tanggal_surat'];
                $note = 'Tanggal keluar mengikuti tanggal surat karena kolom tanggal keluar pada agenda kosong.';
            }
            $this->createRow($payload, $rowIndex + 1, $note);
        }

    }

    private function createRow(array $payload, int $rowNumber, ?string $note = null): void
    {
        $dateField = $this->keluar() ? 'tanggal_keluar_surat' : 'tanggal_masuk_surat';
        $validator = Validator::make($payload, SuratRequest::dataRules($this->keluar()), [$dateField.'.required' => 'Tanggal '.($this->keluar() ? 'keluar' : 'masuk').' surat wajib diisi.']);
        $class = $this->keluar() ? SuratKeluar::class : SuratMasuk::class;
        $status = 'valid';
        $message = 'Siap diimport';
        $key = hash('sha256', json_encode($payload));
        if ($validator->fails()) {
            $status = 'error';
            $message = $validator->errors()->first();
            $this->invalidRows++;
            $this->errors[] = 'Baris '.$rowNumber.': '.$message;
        } else {
            $query = $class::query();
            foreach ($payload as $field => $value) {
                $query->where($field, $value);
            }
            if (isset($this->seen[$key]) || $query->exists()) {
                $status = 'duplicate';
                $message = 'Data yang sama sudah tersedia. Baris dilewati.';
                $this->duplicateRows++;
            } else {
                $this->seen[$key] = true;
                $this->validRows++;
                if (! $this->previewOnly) {
                    if ($this->actorId === null || ! User::whereKey($this->actorId)->exists()) {
                        throw ValidationException::withMessages(['file' => 'Administrator import tidak tersedia. Login kembali.']);
                    }
                    $record = new $class($payload);
                    $record->created_by = $this->actorId;
                    $record->save();
                    $this->created++;
                }
            }
        }
        if ($this->previewOnly) {
            if ($note !== null) {
                $message .= ' '.$note;
            }
            $this->previewRows[] = ['row' => $rowNumber, 'status' => $status, 'message' => $message, 'data' => $payload];
        }
    }

    private function cell(Collection $row, int $index): ?string
    {
        $value = $row->get($index);

        return $value === null || trim((string) $value) === '' ? null : trim((string) $value);
    }

    private function header($value): string
    {
        return str(str_replace("\u{FEFF}", '', $value ?? ''))->lower()->replaceMatches('/[\s\/\.\-]+/u', '_')->replaceMatches('/_+/', '_')->trim('_')->toString();
    }

    private function dateValue(?string $value): ?string
    {
        if ($value === null || $value === '-') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }
            $value = str_replace('/', '-', $value);
            foreach (['!Y-m-d', '!d-m-Y'] as $format) {
                $date = \DateTimeImmutable::createFromFormat($format, $value);
                $errors = \DateTimeImmutable::getLastErrors();
                if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                    return $date->format('Y-m-d');
                }
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }
}
