<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'bulan' => ['nullable', Rule::in(array_merge(['all'], range(1, 12), array_map(fn ($n) => sprintf('%02d', $n), range(1, 12))))],
            'tahun' => ['nullable', 'regex:/^(all|[1-9][0-9]{3})$/'],
        ];
    }
}
