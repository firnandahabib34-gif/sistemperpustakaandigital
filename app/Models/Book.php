<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    
    protected $fillable = [
        'judul',
        'penulis',
        'category_id',   // ← PASTIKAN INI ADA
        'stok',
        'penerbit',
        'tahun'
    ];

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}