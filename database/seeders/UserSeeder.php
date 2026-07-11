<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin (password: admin1)
        User::create([
            'nim' => '3312511017',
            'name' => 'Admin123',
            'email' => 'admin@library.com',
            'password' => Hash::make('admin1'),
            'role' => 'admin',
            'status' => 'aktif'
        ]);

        // Anggota (password: muas123)
        User::create([
            'nim' => '3312511011',
            'name' => 'Muhammad Muas',
            'email' => 'muas@email.com',
            'password' => Hash::make('muas123'),
            'prodi' => 'Teknik Informatika',
            'phone' => '08123456789',
            'role' => 'anggota',
            'status' => 'aktif'
        ]);
    }
}