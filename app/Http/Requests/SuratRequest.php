<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return static::dataRules($this->routeIs('surat-keluar.*')) + [
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'hapus_lampiran' => ['nullable', 'boolean'],
            'created_by' => ['prohibited'],
            'updated_by' => ['prohibited'],
        ];
    }

    public static function dataRules(bool $keluar): array
    {
        return [
            $keluar ? 'tanggal_keluar_surat' : 'tanggal_masuk_surat' => ['required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01', 'before_or_equal:9999-12-31'],
            'nomor_urut' => ['nullable', 'string', 'max:255'],
            $keluar ? 'alamat_penerima' : 'alamat_pengirim' => ['nullable', 'string', 'max:16000'],
            'tanggal_surat' => ['required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01', 'before_or_equal:9999-12-31'],
            'nomor_surat' => ['nullable', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:16000'],
            $keluar ? 'asal' : 'tujuan_disposisi' => ['nullable', 'string', 'max:255'],
        ] + ($keluar ? ['surat_masuk_id' => ['nullable', 'integer', 'exists:surat_masuk,id']] : []);
    }
}
