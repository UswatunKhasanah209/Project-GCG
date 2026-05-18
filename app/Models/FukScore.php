<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FukScore extends Model
{
    protected $fillable = [
        'year',
        'fuk_id',
        'score',
        'scored_by',
        'document_name',
        'page_reference',
        'explanation',
        'assessor_review',
        'recommendation',
    ];

    public function fuk()
    {
        return $this->belongsTo(Fuk::class, 'fuk_id', 'id');
    }

    public function scorer()
    {
        return $this->belongsTo(User::class, 'scored_by');
    }
}
