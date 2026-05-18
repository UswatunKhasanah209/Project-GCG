<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fuk extends Model
{
    protected $table = 'fuks';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'parameter_id',
        'parent_id',
        'name',
        'tipe_penilaian',
        'bobot',
        'required_docs'
    ];

    protected $casts = [
        'bobot' => 'float',
        'required_docs' => 'integer',
    ];

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Fuk::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Fuk::class, 'parent_id', 'id')
            ->orderBy('id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
    public function documents()
    {
        return $this->hasMany(LibraryDocument::class, 'fuk_id', 'id');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isLeaf(): bool
    {
        return !$this->children()->exists();
    }
}
