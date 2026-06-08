<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    
    protected $fillable = [
        'nama',
        'deskripsi'
    ];

    // Relasi ke buku (one to many)
    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}