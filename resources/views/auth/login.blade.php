@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<?php
// ============================================
// FITUR UPLOAD BACKGROUND & LOGO - 1 FILE SAJA
// ============================================

// Cek jika user login sebagai admin
$isAdmin = auth()->check() && auth()->user()->role === 'admin';

// Proses upload background
if ($isAdmin && isset($_POST['upload_background'])) {
    if (isset($_FILES['background']) && $_FILES['background']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['background'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        
        if (in_array($ext, $allowed) && $file['size'] <= 2048 * 1024) {
            $target = public_path('backgrounds/login-bg.jpg');
            $dir = dirname($target);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($file['tmp_name'], $target);
            echo '<script>alert("Background berhasil diupdate!"); window.location.href="'.url()->current().'";</script>';
        }
    }
}

// Proses upload logo
if ($isAdmin && isset($_POST['upload_logo'])) {
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['logo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        
        if (in_array($ext, $allowed) && $file['size'] <= 1024 * 1024) {
            $target = public_path('logos/logo-polibatam.png');
            $dir = dirname($target);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            move_uploaded_file($file['tmp_name'], $target);
            echo '<script>alert("Logo berhasil diupdate!"); window.location.href="'.url()->current().'";</script>';
        }
    }
}

// Cek apakah file ada
$bgExists = file_exists(public_path('backgrounds/login-bg.jpg'));
$logoExists = file_exists(public_path('logos/logo-polibatam.png'));
?>

<!-- BACKGROUND FULLSCREEN -->
<div class="fixed inset-0 z-0" 
     style="background-image: url('{{ asset('backgrounds/login-bg.jpg') }}'); 
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
    <div class="w-full max-w-xl"> <!-- DIPERLEBAR LAGI ke max-w-xl -->
        
        <!-- CARD -->
        <div class="rounded-2xl shadow-2xl overflow-hidden" style="background: #ffffff; opacity: 1;">
            
            <!-- HEADER -->
            <div class="px-8 pt-8 pb-4 text-center" style="background: #ffffff;">
                <!-- LOGO -->
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('logos/logo-polibatam.png') }}" 
                         alt="Logo Polibatam" 
                         class="h-24 w-auto object-contain"
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-24 h-24 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white text-4xl shadow-lg\'><i class=\'fas fa-university\'></i></div>'">
                </div>
                
                <h2 class="text-2xl font-bold text-gray-800">E-Library</h2>
                <p class="text-gray-500 text-sm mt-1">Politeknik Negeri Batam</p>
                <div class="mt-2 inline-block px-4 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login ke Akun Anda
                </div>
            </div>
            
            <!-- FORM -->
            <div class="px-8 pb-8" style="background: #ffffff;">
                
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

                <!-- PANEL ADMIN -->
                @if($isAdmin)
                <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-blue-700">
                            <i class="fas fa-cog mr-2"></i>Pengaturan Tampilan (Admin)
                        </h4>
                        <span class="text-xs bg-blue-200 text-blue-700 px-2 py-0.5 rounded-full">Admin Only</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg p-3 border border-blue-100">
                            <input type="hidden" name="upload_background" value="1">
                            <label class="text-xs font-medium text-gray-700 block mb-1">Background</label>
                            <input type="file" name="background" accept="image/*" class="text-xs w-full mb-1" required>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium py-1.5 rounded transition">
                                <i class="fas fa-upload mr-1"></i> Upload
                            </button>
                        </form>
                        
                        <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg p-3 border border-blue-100">
                            <input type="hidden" name="upload_logo" value="1">
                            <label class="text-xs font-medium text-gray-700 block mb-1">Logo</label>
                            <input type="file" name="logo" accept="image/*" class="text-xs w-full mb-1" required>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium py-1.5 rounded transition">
                                <i class="fas fa-upload mr-1"></i> Upload
                            </button>
                        </form>
                    </div>
                    
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <div class="bg-white rounded p-1 border border-blue-100">
                            <p class="text-[10px] text-gray-500 text-center">Preview Background</p>
                            <div class="h-12 rounded bg-cover bg-center" 
                                 style="background-image: url('{{ asset($bgExists ? 'backgrounds/login-bg.jpg' : '') }}');">
                            </div>
                        </div>
                        <div class="bg-white rounded p-1 border border-blue-100 text-center">
                            <p class="text-[10px] text-gray-500">Preview Logo</p>
                            <img src="{{ asset($logoExists ? 'logos/logo-polibatam.png' : '') }}" 
                                 alt="Logo" 
                                 class="h-10 mx-auto object-contain">
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('login.process') }}">
                    @csrf
                    
                    <!-- NIM -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-user text-blue-600 mr-2"></i>Username
                            <span class="text-gray-400 font-normal text-xs">(NIM/NIP)</span>
                        </label>
                        <input type="text" name="nim" value="{{ old('nim') }}" 
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 @error('nim') border-red-400 @enderror"
                            placeholder="Masukkan NIM">
                        @error('nim')
                            <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-lock text-blue-600 mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 pr-12"
                                placeholder="Masukkan password">
                            <button type="button" id="togglePassword" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors duration-200">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pilih Peran -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <i class="fas fa-user-tag text-blue-600 mr-2"></i>Pilih Peran
                        </label>
                        <select name="role" 
                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 appearance-none cursor-pointer">
                            <option value="">-- Pilih Peran --</option>
                            <option value="anggota" {{ old('role') == 'anggota' ? 'selected' : '' }}>🎓 Mahasiswa</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>👨‍🏫 Admin</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Login Button -->
                    <button type="submit" 
                        class="w-full py-3.5 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2" 
                        style="background: linear-gradient(135deg, #1a56db, #1e40af); color: #ffffff !important; font-size: 16px; border: none;">
                        <i class="fas fa-sign-in-alt" style="color: #ffffff !important;"></i>
                        <span style="color: #ffffff !important;">Login</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">atau</span>
                    </div>
                </div>

                <!-- Register & Back to Home -->
                <div class="text-center space-y-3">
                    <p class="text-sm text-gray-600">
                        Belum punya akun? 
                        <a href="/register" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline transition-colors duration-200">
                            Daftar Sekarang
                        </a>
                    </p>
                    
                    <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200 text-sm border border-gray-200">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>

                <!-- Copyright -->
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
    
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }
    
    /* PERBAIKAN INPUT AGAR LEBIH LEBAR */
    .form-control {
        width: 100% !important;
        padding: 12px 16px !important;
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                if (type === 'text') {
                    icon.className = 'fas fa-eye-slash';
                } else {
                    icon.className = 'fas fa-eye';
                }
            });
        }
    });
</script>

@endsection