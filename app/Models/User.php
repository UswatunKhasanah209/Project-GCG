<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'division',
        'department',
        'bagian',
        'role',
        'division_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi Divisi
    |--------------------------------------------------------------------------
    | Jangan diberi nama division(), karena di tabel users sudah ada kolom
    | bernama division. Kalau namanya sama, Laravel bisa bentrok saat akses data.
    */
    public function divisionData()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Nama Divisi
    |--------------------------------------------------------------------------
    | Ambil nama divisi dari tabel divisions berdasarkan division_id.
    */
    public function getDivisionNameAttribute()
    {
        if ($this->relationLoaded('divisionData') && $this->divisionData) {
            return $this->divisionData->name;
        }

        if ($this->divisionData()->exists()) {
            return $this->divisionData()->first()->name;
        }

        return '-';
    }
}