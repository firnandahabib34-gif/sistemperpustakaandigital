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

// halaman register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');