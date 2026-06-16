<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run()
    {
        Book::create([
            'judul' => 'Laravel 11',
            'penulis' => 'Taylor Otwell',
            'category_id' => 1,
            'stok' => 5,
            'penerbit' => "O'Reilly Media",
            'tahun' => 2024
        ]);

        Book::create([
            'judul' => 'Tailwind CSS',
            'penulis' => 'Adam Wathan',
            'category_id' => 1,
            'stok' => 3,
            'penerbit' => 'Tailwind Labs',
            'tahun' => 2023
        ]);
    }
}