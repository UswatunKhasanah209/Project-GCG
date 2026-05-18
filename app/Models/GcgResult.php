<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GcgResult extends Model
{
    protected $table = 'gcg_results';

    protected $fillable = [
        'year',
        'level',     // fuk | parameter | indikator | aspek
        'entity_id', // id level tersebut (Fxx/Pxx/Ixx/Axx)
        'score',
    ];

    protected $casts = [
        'year' => 'integer',
        'score' => 'float',
    ];

    // optional: biar gampang dipakai
    public const LEVEL_FUK = 'fuk';
    public const LEVEL_PARAMETER = 'parameter';
    public const LEVEL_INDIKATOR = 'indikator';
    public const LEVEL_ASPEK = 'aspek';
}