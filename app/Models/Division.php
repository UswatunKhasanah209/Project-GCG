<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function documents()
    {
        return $this->hasMany(\App\Models\LibraryDocument::class);
    }
}
