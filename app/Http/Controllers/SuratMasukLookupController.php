<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;

class SuratMasukLookupController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:255']]);
        $query = SuratMasuk::query();
        if (! empty($data['q'])) {
            $search = $data['q'];
            $query->where(fn ($q) => $q->where('nomor_surat', 'like', '%'.$search.'%')
                ->orWhere('perihal', 'like', '%'.$search.'%')
                ->orWhere('alamat_pengirim', 'like', '%'.$search.'%'));
        }

        return response()->json($query->latest('tanggal_masuk_surat')->latest('id')->limit(20)
            ->get(['id', 'nomor_surat', 'perihal', 'alamat_pengirim']));
    }
}
