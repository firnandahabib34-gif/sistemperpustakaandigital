<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Teknologi', 'Matematika', 'Fisika', 'Kimia', 'Bahasa'];
        
        foreach ($categories as $cat) {
            Category::create([
                'nama' => $cat,
                'deskripsi' => 'Kategori ' . $cat
            ]);
        }
    }
}