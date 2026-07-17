@extends('layouts.app')

@section('title', 'Pengaturan Tampilan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg p-6">
        
        <!-- HEADER -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-cog mr-2 text-blue-600"></i> Pengaturan Tampilan
        </h1>

        <!-- ALERT PESAN -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        <!-- GRID 2 KOLOM -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- KOLOM 1: UPLOAD BACKGROUND -->
            <div class="border-2 border-gray-200 rounded-xl p-6">
                
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-image text-blue-600 mr-2"></i> Background Login
                </h3>
                
                <!-- PREVIEW BACKGROUND -->
                <div class="mb-4">
                    @php
                        $bgExists = file_exists(public_path('backgrounds/login-bg.jpg'));
                    @endphp
                    <div class="h-40 rounded-lg bg-cover bg-center border-2 border-gray-300" 
                         style="background-image: url('{{ asset($bgExists ? 'backgrounds/login-bg.jpg' : 'images/default-bg.jpg') }}'); 
                                background-size: cover;
                                background-position: center;">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Status: {{ $bgExists ? '✅ Tersedia' : '❌ Belum ada' }}
                    </p>
                </div>

                <!-- FORM UPLOAD BACKGROUND -->
                <form method="POST" action="{{ route('admin.settings.upload-background') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File Background:
                        </label>
                        <input type="file" name="background" accept="image/*" 
                               class="w-full text-sm border border-gray-300 rounded-lg p-2 cursor-pointer" 
                               required>
                        <p class="text-xs text-gray-500 mt-1">
                            ⚠️ Maks: 2MB | Format: JPG, PNG, GIF, WEBP
                        </p>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-upload mr-2"></i> Upload Background
                    </button>
                </form>
            </div>

            <!-- KOLOM 2: UPLOAD LOGO -->
            <div class="border-2 border-gray-200 rounded-xl p-6">
                
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-image text-blue-600 mr-2"></i> Logo
                </h3>
                
                <!-- PREVIEW LOGO -->
                <div class="mb-4">
                    @php
                        $logoExists = file_exists(public_path('logos/logo-polibatam.png'));
                    @endphp
                    <div class="h-40 flex items-center justify-center border-2 border-gray-300 rounded-lg bg-white">
                        @if($logoExists)
                            <img src="{{ asset('logos/logo-polibatam.png') }}" 
                                 alt="Logo" 
                                 class="h-28 w-auto object-contain">
                        @else
                            <div class="text-center">
                                <i class="fas fa-image text-gray-300 text-4xl mb-2"></i>
                                <p class="text-gray-400 text-sm">Belum ada logo</p>
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Status: {{ $logoExists ? '✅ Tersedia' : '❌ Belum ada' }}
                    </p>
                </div>

                <!-- FORM UPLOAD LOGO -->
                <form method="POST" action="{{ route('admin.settings.upload-logo') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File Logo:
                        </label>
                        <input type="file" name="logo" accept="image/*" 
                               class="w-full text-sm border border-gray-300 rounded-lg p-2 cursor-pointer" 
                               required>
                        <p class="text-xs text-gray-500 mt-1">
                            ⚠️ Maks: 1MB | Format: JPG, PNG, GIF, WEBP
                        </p>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-upload mr-2"></i> Upload Logo
                    </button>
                </form>
            </div>
        </div>

        <!-- INFORMASI TAMBAHAN -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i> Informasi
            </h4>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>
                    <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                    File akan disimpan di folder <code class="bg-gray-200 px-1 rounded">public/backgrounds/</code> dan <code class="bg-gray-200 px-1 rounded">public/logos/</code>
                </li>
                <li>
                    <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                    Background akan otomatis ditampilkan di halaman login
                </li>
                <li>
                    <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                    Logo akan otomatis ditampilkan di halaman login
                </li>
                <li>
                    <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                    File akan diganti dengan nama yang sama setiap upload
                </li>
            </ul>
        </div>
        
    </div>
</div>
@endsection