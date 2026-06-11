<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'user_id', 'book_id', 'admin_id', 'borrow_date', 'due_date',
        'return_date', 'status', 'fine'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function calculateFine()
    {
        if ($this->status !== 'dipinjam') {
            return 0;
        }

        $today = now();
        $dueDate = $this->due_date;

        if ($today > $dueDate) {
            $daysLate = $today->diffInDays($dueDate);
            return $daysLate * 2000;
        }

        return 0;
    }
}