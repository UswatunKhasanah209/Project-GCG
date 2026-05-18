<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspek extends Model
{
    protected $table = 'aspeks';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
    ];

    public function indikators()
    {
        return $this->hasMany(Indikator::class, 'aspect_id', 'id')
            ->orderBy('id');
    }

    public function getRomanIdAttribute(): string
    {
        $value = strtoupper((string) $this->id);

        $map = [
            'A1' => 'I',
            'A2' => 'II',
            'A3' => 'III',
            'A4' => 'IV',
            'A5' => 'V',
            'A6' => 'VI',
            '1'  => 'I',
            '2'  => 'II',
            '3'  => 'III',
            '4'  => 'IV',
            '5'  => 'V',
            '6'  => 'VI',
            'I'  => 'I',
            'II' => 'II',
            'III' => 'III',
            'IV' => 'IV',
            'V'  => 'V',
            'VI' => 'VI',
        ];

        return $map[$value] ?? (string) $this->id;
    }

    public function getDisplayNameAttribute(): string
    {
        return 'ASPEK ' . $this->roman_id;
    }
}