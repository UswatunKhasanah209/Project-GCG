<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    protected $table = 'parameters';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'indikator_id',
        'name',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'indikator_id', 'id');
    }

    public function fuks()
    {
        return $this->hasMany(Fuk::class, 'parameter_id', 'id')
            ->orderBy('id');
    }
}
