<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// ROUTE AUTHENTICATION (LOGIN, REGISTER, LOGOUT)
// ============================================================

// Halaman Login & Proses Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Halaman Register & Proses Register
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

// Logout (POST - wajib pakai POST untuk security)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================================
// ROUTE PUBLIK (LANDING PAGE)
// ============================================================

Route::get('/', function () {
    return view('landing');
});

// ============================================================
// ROUTE DASHBOARD
// ============================================================

Route::get('/dashboard-admin', function () {
    return view('admin.dashboard');
})->middleware('auth');

Route::get('/dashboard-anggota', function () {
    return view('anggota.dashboard');
})->middleware('auth');

// ============================================================
// ROUTE UNTUK ADMIN (CRUD DATABASE)
// ============================================================
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    
    // CRUD Buku
    Route::get('/books', [BookController::class, 'index'])->name('books');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');
    
    // CRUD Kategori
    Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [CategoryController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [CategoryController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [CategoryController::class, 'destroy'])->name('kategori.destroy');
    
    // CRUD Anggota
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{id}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{id}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{id}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    Route::patch('/anggota/{id}/toggle-status', [AnggotaController::class, 'toggleStatus'])->name('anggota.toggle-status');
});

// ============================================================
// ROUTE UNTUK ADMIN (TAMPILAN - LOCALSTORAGE)
// ============================================================
Route::get('/admin/loans', function () {
    return view('admin.loans.index');
})->name('admin.loans')->middleware('auth');

Route::get('/admin/pengembalian', function () {
    return view('admin.pengembalian.index');
})->name('admin.pengembalian')->middleware('auth');

Route::get('/admin/laporan', function () {
    return view('admin.laporan.index');
})->name('admin.laporan')->middleware('auth');

// ============================================================
// ROUTE UNTUK ANGGOTA
// ============================================================
Route::get('/dashboard-anggota/buku', function () {
    return view('anggota.buku');
})->middleware('auth');

Route::get('/dashboard-anggota/loans', function () {
    return view('anggota.loans');
})->name('anggota.loans')->middleware('auth');

// ============================================================
// API ROUTES (Untuk Ambil Data JSON)
// ============================================================
Route::get('/api/categories', function () {
    return App\Models\Category::all();
});

Route::get('/api/books', function () {
    return App\Models\Book::with('category')->get();
});

Route::get('/api/anggota', function () {
    return App\Models\User::where('role', 'anggota')->get();
});