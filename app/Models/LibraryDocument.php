<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryDocument extends Model
{
    protected $fillable = [
        'division_id',
        'uploader_user_id',
        'year',
        'aspek_id',
        'indikator_id',
        'parameter_id',
        'fuk_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'review_status',
        'reviewed_by',
        'review_note',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function aspek()
    {
        return $this->belongsTo(Aspek::class, 'aspek_id');
    }

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id');
    }

    public function fuk()
    {
        return $this->belongsTo(Fuk::class, 'fuk_id');
    }
}