<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FukStatus extends Model
{
    protected $fillable = [
        'division_id',
        'fuk_id',
        'year',
        'status',
        'note',
        'updated_by',
    ];
}