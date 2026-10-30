<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuk';

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function pengubahTerakhir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function balasan(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'surat_masuk_id');
    }

    protected $fillable = [
        'tanggal_masuk_surat',
        'nomor_urut',
        'alamat_pengirim',
        'tanggal_surat',
        'nomor_surat',
        'perihal',
        'tujuan_disposisi',
        'lampiran_path',
        'lampiran_nama_asli',
        'lampiran_mime',
        'lampiran_ukuran',
    ];

    public function getLampiranUrlAttribute(): ?string
    {
        return $this->lampiran_path ? asset('storage/'.$this->lampiran_path) : null;
    }

    public function getLampiranUkuranLabelAttribute(): ?string
    {
        if (! $this->lampiran_ukuran) {
            return null;
        }

        if ($this->lampiran_ukuran >= 1024 * 1024) {
            return number_format($this->lampiran_ukuran / 1024 / 1024, 1).' MB';
        }

        return number_format($this->lampiran_ukuran / 1024, 0).' KB';
    }
}
