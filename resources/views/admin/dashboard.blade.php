@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<nav class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
    <div class="container mx-auto px-6 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-book text-white text-2xl"></i>
                <span class="text-white font-bold text-xl">Perpustakaan Digital - Admin</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white">Halo, Admin</span>
                <a href="/login" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container mx-auto px-6 py-8">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Buku</p>
                    <p class="text-2xl font-bold">5</p>
                </div>
                <i class="fas fa-book text-blue-500 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Anggota</p>
                    <p class="text-2xl font-bold">5</p>
                </div>
                <i class="fas fa-users text-green-500 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Peminjaman Aktif</p>
                    <p class="text-2xl font-bold">1</p>
                </div>
                <i class="fas fa-clock text-yellow-500 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Menunggu Persetujuan</p>
                    <p class="text-2xl font-bold">1</p>
                </div>
                <i class="fas fa-hourglass-half text-orange-500 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total Denda</p>
                    <p class="text-2xl font-bold">Rp 0</p>
                </div>
                <i class="fas fa-money-bill text-red-500 text-3xl"></i>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="#" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-book-open text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Manajemen Buku</h3>
                    <p class="text-gray-500 text-sm">Tambah, edit, hapus buku</p>
                </div>
            </div>
        </a>
        
        <a href="#" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-tags text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Manajemen Kategori</h3>
                    <p class="text-gray-500 text-sm">Kelola kategori buku</p>
                </div>
            </div>
        </a>
        
        <a href="#" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-hand-holding-heart text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Peminjaman</h3>
                    <p class="text-gray-500 text-sm">Proses peminjaman buku</p>
                </div>
            </div>
        </a>
        
        <a href="#" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-undo-alt text-red-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Pengembalian</h3>
                    <p class="text-gray-500 text-sm">Proses pengembalian buku</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection