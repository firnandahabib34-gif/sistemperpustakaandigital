<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'user_id',
        'book_id',
        'admin_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine',
        'anggota_confirmed',
        'extended_at',
        'extended_count',
        'extend_status',
        'extend_requested_at'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'anggota_confirmed' => 'boolean',
        'extended_at' => 'datetime',
        'extend_requested_at' => 'datetime',
    ];

    // Relasi ke user (anggota)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke buku
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    // Relasi ke admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}