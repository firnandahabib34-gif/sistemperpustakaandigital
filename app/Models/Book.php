<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    
   protected $fillable = [
    'kode_buku',      
    'judul',
    'penulis',
    'category_id',
    'stok',
    'penerbit',
    'tahun',
    'isbn',
    'lokasi_rak',
    'deskripsi',
    'jumlah_halaman',
    'sampul'
];

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Relasi ke peminjaman
    public function loans()
    {
        return $this->hasMany(Loan::class, 'book_id');
    }

    /**
     * Mengurangi stok buku
     * @param int $jumlah
     * @return bool
     */
    public function kurangiStok($jumlah = 1)
    {
        $this->stok -= $jumlah;
        return $this->save();
    }

    /**
     * Menambah stok buku
     * @param int $jumlah
     * @return bool
     */
    public function tambahStok($jumlah = 1)
    {
        $this->stok += $jumlah;
        return $this->save();
    }

    /**
     * Cek ketersediaan buku
     * @return bool
     */
    public function isTersedia()
    {
        return $this->stok > 0;
    }
}