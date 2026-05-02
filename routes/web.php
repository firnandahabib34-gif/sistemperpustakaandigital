<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('landing');
});

// Halaman Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Proses Login (dihandle oleh JavaScript, bukan backend)
// Route::post('/login', [AuthController::class, 'login']); // ← HAPUS atau COMMENT

// Logout
Route::get('/logout', function () {
    return redirect('/login');
})->name('logout');

// Halaman Register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Dashboard Admin
Route::get('/dashboard-admin', function () {
    return view('admin.dashboard');
});

// Halaman Kelola Buku (Admin)
Route::get('/admin/books', function () {
    return view('admin.books.index');
})->name('admin.books');

// Halaman Kelola Anggota (Admin)
Route::get('/admin/anggota', function () {
    return view('admin.anggota.index');
})->name('admin.anggota');

// Halaman Peminjaman (Admin)
Route::get('/admin/loans', function () {
    return view('admin.loans.index');
})->name('admin.loans');

// Halaman Pengembalian (Admin)
Route::get('/admin/pengembalian', function () {
    return view('admin.pengembalian.index');
})->name('admin.pengembalian');

// Dashboard Anggota (halaman statistik)
Route::get('/dashboard-anggota', function () {
    return view('anggota.dashboard');
});

// Koleksi Buku Anggota
Route::get('/dashboard-anggota/buku', function () {
    return view('anggota.buku');
});

// Peminjaman Saya (Anggota)
Route::get('/dashboard-anggota/loans', function () {
    return view('anggota.loans');
})->name('anggota.loans');