<?php

namespace App\Services;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class SuratService
{
    public function __construct(private AttachmentCleanup $cleanup) {}

    public function save(string $class, array $payload, int $actorId, ?Model $surat = null, ?UploadedFile $file = null, bool $removeAttachment = false): Model
    {
        $attachment = [];
        if ($file) {
            $path = $file->store($class === SuratMasuk::class ? 'surat-masuk' : 'surat-keluar', 'public');
            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages(['lampiran' => 'Lampiran tidak dapat disimpan. Periksa ruang penyimpanan dan coba kembali.']);
            }
            $attachment = [
                'lampiran_path' => $path,
                'lampiran_nama_asli' => $file->getClientOriginalName(),
                'lampiran_mime' => $file->getMimeType(),
                'lampiran_ukuran' => $file->getSize(),
            ];
        }

        try {
            return DB::transaction(function () use ($class, $payload, $actorId, $surat, $attachment, $removeAttachment) {
                User::whereKey($actorId)->lockForUpdate()->firstOrFail();
                $record = $surat ? $class::whereKey($surat->getKey())->lockForUpdate()->firstOrFail() : new $class;
                $oldPath = $record->lampiran_path;
                $record->fill($payload);
                if ($surat) {
                    $record->updated_by = $actorId;
                } else {
                    $record->created_by = $actorId;
                }
                if ($removeAttachment || $attachment !== []) {
                    $record->forceFill(array_fill_keys(['lampiran_path', 'lampiran_nama_asli', 'lampiran_mime', 'lampiran_ukuran'], null));
                    $record->forceFill($attachment);
                }
                $record->save();
                if ($oldPath && $oldPath !== $record->lampiran_path) {
                    DB::afterCommit(fn () => $this->cleanup->delete($oldPath));
                }

                return $record;
            }, 3);
        } catch (Throwable $exception) {
            $this->cleanup->delete($attachment['lampiran_path'] ?? null);
            if ($exception instanceof QueryException && ($exception->errorInfo[1] ?? null) === 1452) {
                throw ValidationException::withMessages(['surat_masuk_id' => 'Surat masuk asal sudah tidak tersedia. Pilih kembali surat yang akan dibalas.']);
            }
            throw $exception;
        }
    }

    public function delete(Model $surat): void
    {
        $this->remove($surat::class, $surat->getKey());
    }

    public function reset(string $class): void
    {
        $this->remove($class);
    }

    private function remove(string $class, ?int $id = null): void
    {
        try {
            DB::transaction(function () use ($class, $id) {
                $query = $class::query()->when($id !== null, fn ($query) => $query->whereKey($id));
                $records = (clone $query)->lockForUpdate()->get();
                if ($id !== null && $records->isEmpty()) {
                    abort(404);
                }
                if ($class === SuratMasuk::class && SuratKeluar::whereIn('surat_masuk_id', $records->modelKeys())->exists()) {
                    throw ValidationException::withMessages(['surat' => 'Surat masuk masih memiliki surat keluar balasan. Lepaskan hubungan balasan terlebih dahulu sebelum menghapus atau mereset data.']);
                }
                $paths = $records->pluck('lampiran_path')->filter()->all();
                $query->delete();
                DB::afterCommit(function () use ($paths) {
                    foreach ($paths as $path) {
                        $this->cleanup->delete($path);
                    }
                });
            }, 3);
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451) {
                throw ValidationException::withMessages(['surat' => 'Data masih dirujuk oleh surat balasan dan tidak dapat dihapus.']);
            }
            throw $exception;
        }
    }
}
