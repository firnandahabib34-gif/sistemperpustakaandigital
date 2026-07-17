@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<?php
$bgExists = file_exists(public_path('backgrounds/login-bg.jpg'));
$logoExists = file_exists(public_path('logos/logo-polibatam.png'));
?>

<!-- BACKGROUND FULLSCREEN -->
<div class="fixed inset-0 z-0" 
     style="background-image: url('{{ asset($bgExists ? 'backgrounds/login-bg.jpg' : 'images/default-bg.jpg') }}'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            background-attachment: fixed;
            filter: blur(2px) brightness(0.9);">
    
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,0,0,0.75), rgba(0,0,0,0.6), rgba(0,0,0,0.7), rgba(30,30,60,0.5));"></div>
    <div class="absolute inset-0" style="background: radial-gradient(ellipse at 30% 50%, rgba(100,150,255,0.05) 0%, transparent 70%), radial-gradient(ellipse at 70% 50%, rgba(100,150,255,0.05) 0%, transparent 70%);"></div>
</div>

<!-- KONTEN LOGIN -->
<div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-xl">
        
        <!-- CARD -->
        <div class="rounded-2xl shadow-2xl overflow-hidden" style="background: #ffffff;">
            
            <!-- HEADER dengan LOGO -->
            <div class="px-8 pt-8 pb-4 text-center" style="background: #ffffff;">
                <!-- LOGO -->
                <div class="flex justify-center mb-4">
                    @if($logoExists)
                        <img src="{{ asset('logos/logo-polibatam.png') }}" 
                             alt="Logo Polibatam" 
                             class="h-24 w-auto object-contain">
                    @else
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white text-4xl shadow-lg">
                            <i class="fas fa-university"></i>
                        </div>
                    @endif
                </div>
                
                <h2 class="text-2xl font-bold text-gray-800">E-Library</h2>
                <p class="text-gray-500 text-sm mt-1">Politeknik Negeri Batam</p>
                <div class="mt-2 inline-block px-4 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login ke Akun Anda
                </div>
            </div>
            
            <!-- FORM LOGIN -->
            <div class="px-8 pb-8" style="background: #ffffff;">
                
                <!-- ALERT MESSAGES -->
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
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM -->
                <form method="POST" action="{{ route('login.process') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-user text-blue-600 mr-2"></i>Username
                            <span class="text-gray-400 font-normal text-xs">(NIM/NIP)</span>
                        </label>
                        <input type="text" name="nim" value="{{ old('nim') }}" 
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 @error('nim') border-red-400 @enderror"
                            placeholder="Masukkan NIM atau NIP" required>
                        @error('nim')
                            <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-lock text-blue-600 mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 pr-12"
                                placeholder="Masukkan password" required>
                            <button type="button" id="togglePassword" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors duration-200">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-user-tag text-blue-600 mr-2"></i>Pilih Peran
                        </label>
                        <select name="role" 
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 appearance-none cursor-pointer" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="anggota" {{ old('role') == 'anggota' ? 'selected' : '' }}>🎓 Mahasiswa</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>👨‍🏫 Admin</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                        class="w-full py-3.5 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2" 
                        style="background: linear-gradient(135deg, #1a56db, #1e40af); border: none;">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
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

                <div class="text-center space-y-3">
                    <p class="text-sm text-gray-600">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline transition-colors duration-200">
                            Daftar Sekarang
                        </a>
                    </p>
                    
                    <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200 text-sm border border-gray-200">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Politeknik Negeri Batam</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- STYLE -->
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

<!-- JAVASCRIPT - YANG DIPERBAIKI -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================
        // FITUR TOGGLE PASSWORD
        // ============================================
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                // Toggle type password
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                if (icon) {
                    icon.className = type === 'text' ? 'fas fa-eye-slash' : 'fas fa-eye';
                }
            });
        }

        // ============================================
        // AUTO-HIDE ALERTS (DIPERBAIKI)
        // ============================================
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }, 5000);
        });
    });
</script>

@endsection