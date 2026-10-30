<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

abstract class AgendaImportController extends Controller
{
    abstract protected function kind(): string;

    abstract protected function importer(): string;

    public function create()
    {
        return view('surat_'.$this->kind().'.import');
    }

    public function store(Request $request)
    {
        $kind = $this->kind();
        $class = $this->importer();
        $sessionKey = 'surat_'.$kind.'_import';
        if ($request->has('import_token')) {
            $data = $request->validate(['import_token' => ['required', 'uuid']]);
            $preview = session($sessionKey);
            if (! $preview || ($preview['user_id'] ?? null) !== $request->user()->id || ! hash_equals($preview['token'], $data['import_token'])) {
                throw ValidationException::withMessages(['file' => 'Sesi preview import tidak valid atau sudah digunakan. Upload file kembali.']);
            }
            if (! Storage::disk('local')->exists($preview['path'])) {
                session()->forget($sessionKey);
                throw ValidationException::withMessages(['file' => 'File preview tidak ditemukan. Upload file kembali.']);
            }
            $import = new $class(actorId: $request->user()->id);
            try {
                DB::transaction(function () use ($request, $import, $preview) {
                    User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
                    Excel::import($import, Storage::disk('local')->path($preview['path']));
                });
            } catch (Throwable $exception) {
                if ($exception instanceof ValidationException) {
                    throw $exception;
                }
                report($exception);
                throw ValidationException::withMessages(['file' => 'Impor belum dapat disimpan. Tidak ada perubahan yang diterapkan. Periksa data dan coba kembali.']);
            }
            session()->forget($sessionKey);
            Storage::disk('local')->delete($preview['path']);
            $message = $import->created.' data berhasil diimport.';

            return redirect()->route('surat-'.$kind.'.index')
                ->with($import->errors === [] ? 'success' : 'warning', $message.($import->errors === [] ? '' : ' Beberapa baris tidak valid dilewati.'))
                ->with('import_errors', $import->errors);
        }
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);
        $token = (string) Str::uuid();
        $path = $request->file('file')->storeAs('import-preview/surat-'.$kind, $token.'.'.$request->file('file')->getClientOriginalExtension(), 'local');
        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages(['file' => 'Berkas impor tidak dapat disimpan. Periksa ruang penyimpanan dan coba kembali.']);
        }
        $import = new $class(previewOnly: true);
        try {
            Excel::import($import, Storage::disk('local')->path($path));
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);
            if ($exception instanceof ValidationException) {
                throw $exception;
            }
            report($exception);
            throw ValidationException::withMessages(['file' => 'Berkas tidak dapat dibaca. Pastikan format dan isi berkas Excel/CSV sesuai template.']);
        }
        $old = session($sessionKey);
        if ($old) {
            Storage::disk('local')->delete($old['path']);
        }
        session([$sessionKey => ['token' => $token, 'path' => $path, 'user_id' => $request->user()->id]]);

        return view('surat_'.$kind.'.import', [
            'previewRows' => $import->previewRows, 'validRows' => $import->validRows,
            'duplicateRows' => $import->duplicateRows, 'invalidRows' => $import->invalidRows,
            'importToken' => $token, 'fileName' => $request->file('file')->getClientOriginalName(),
            'selectedSheet' => $import->selectedSheet,
        ]);
    }
}
