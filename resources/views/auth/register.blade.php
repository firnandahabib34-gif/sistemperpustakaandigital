@extends('layouts.auth')

@section('title', 'Register')

@section('content')

<?php
// Cek apakah background ada
$bgExists = file_exists(public_path('backgrounds/login-bg.jpg'));
?>

<!-- BACKGROUND FULLSCREEN -->
<div class="fixed inset-0 z-0" 
     style="background-image: url('{{ asset($bgExists ? 'backgrounds/login-bg.jpg' : '') }}'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            background-attachment: fixed;
            filter: blur(2px) brightness(0.9);">
    
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,0,0,0.75), rgba(0,0,0,0.6), rgba(0,0,0,0.7), rgba(30,30,60,0.5));"></div>
    <div class="absolute inset-0" style="background: radial-gradient(ellipse at 30% 50%, rgba(100,150,255,0.05) 0%, transparent 70%), radial-gradient(ellipse at 70% 50%, rgba(100,150,255,0.05) 0%, transparent 70%);"></div>
</div>

<!-- KONTEN -->
<div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl"> <!-- DIPERLEBAR LAGI ke max-w-2xl -->

        <!-- CARD -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden" style="background: #ffffff; opacity: 1;">

            <div class="p-8" style="background: #ffffff;"> <!-- PADDING DIKEMBALIKAN ke p-8 agar proporsional -->
                
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Daftar Akun</h2>
                    <p class="text-sm text-gray-500">Bergabung dengan perpustakaan</p>
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center text-sm">
                        <i class="fas fa-check-circle mr-2 text-green-500"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center text-sm">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center text-sm">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                        @foreach($errors->all() as $error)
                            <span class="block">{{ $error }}</span>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register.process') }}" class="space-y-4">
                    @csrf
                    
                    <!-- 2 KOLOM: NIM + Nama Lengkap -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">NIM *</label>
                            <input type="text" name="nim" value="{{ old('nim') }}" 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 @error('nim') border-red-400 @enderror" 
                                placeholder="Masukkan NIM" required>
                            @error('nim') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 @error('name') border-red-400 @enderror" 
                                placeholder="Masukkan nama lengkap" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- 2 KOLOM: Email + No Telepon -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 @error('email') border-red-400 @enderror" 
                                placeholder="Masukkan email" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300" 
                                placeholder="Masukkan nomor telepon">
                        </div>
                    </div>

                    <!-- Program Studi (1 kolom penuh) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Program Studi</label>
                        <select name="prodi" 
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 appearance-none cursor-pointer">
                            <option value="">Pilih Program Studi</option>
                            <option value="Teknik Informatika" {{ old('prodi') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                            <option value="Sistem Informasi" {{ old('prodi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                            <option value="Teknik Komputer" {{ old('prodi') == 'Teknik Komputer' ? 'selected' : '' }}>Teknik Komputer</option>
                            <option value="Manajemen Informatika" {{ old('prodi') == 'Manajemen Informatika' ? 'selected' : '' }}>Manajemen Informatika</option>
                        </select>
                    </div>

                    <!-- 2 KOLOM: Password + Konfirmasi -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Password *</label>
                            <input type="password" name="password" 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 @error('password') border-red-400 @enderror" 
                                placeholder="Masukkan password" required>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300" 
                                placeholder="Konfirmasi password" required>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full py-3.5 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2" 
                        style="background: linear-gradient(135deg, #1a56db, #1e40af); color: #ffffff !important; font-size: 16px; border: none;">
                        <i class="fas fa-user-plus" style="color: #ffffff !important;"></i>
                        <span style="color: #ffffff !important;">Daftar</span>
                    </button>
                </form>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">atau</span>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="/login" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline transition-colors duration-200">
                            Login
                        </a>
                    </p>
                    
                    <div class="mt-3">
                        <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200 text-sm border border-gray-200">
                            <i class="fas fa-home mr-2"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Politeknik Negeri Batam</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 100vh !important;
        background: transparent !important;
        overflow-x: hidden !important;
    }
    
    .auth-wrapper, .auth-container, [class*="bg-"] {
        background: transparent !important;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .animate__fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
    }
</style>

@endsection