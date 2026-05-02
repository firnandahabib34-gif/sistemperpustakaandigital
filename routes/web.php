<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('landing');
});

// halaman login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// proses login
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

//Logout
Route::get('/logout', function () {
    return redirect('/login');
})->name('logout');

// halaman register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Dashboard admin
Route::get('/dashboard-admin', function () {
    return view('admin.dashboard');
});

//Halaman management buku
Route::get('/admin/books', function () {
    return view('admin.books.index');
})->name('admin.books');