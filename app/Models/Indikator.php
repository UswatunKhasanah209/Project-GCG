<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $table = 'indikators';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'aspect_id',
        'name',
    ];

    public function aspect()
    {
        return $this->belongsTo(Aspek::class, 'aspect_id', 'id');
    }

    public function parameters()
    {
        return $this->hasMany(Parameter::class, 'indikator_id', 'id')
            ->orderBy('id');
    }
}
