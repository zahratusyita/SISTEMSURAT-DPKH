<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function suratMasukDibuat(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'created_by');
    }

    public function suratKeluarDibuat(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'created_by');
    }

    public function suratMasukDiubah(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'updated_by');
    }

    public function suratKeluarDiubah(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'updated_by');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
