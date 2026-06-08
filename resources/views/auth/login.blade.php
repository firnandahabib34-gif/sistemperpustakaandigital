@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg">

    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Login ke E-Library</h2>
        <p class="text-sm text-gray-500">Masuk ke sistem perpustakaan</p>
    </div>
    
    <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
    @csrf
    
    <div>
        <label class="block text-sm font-medium text-gray-700">NIM</label>
        <input type="text" name="nim" value="{{ old('nim') }}" 
            class="w-full mt-1 p-2 border rounded-lg @error('nim') border-red-500 @enderror" required>
        @error('nim')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" 
            class="w-full mt-1 p-2 border rounded-lg" required>
    </div>

    <button type="submit" class="w-full bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition">
        Login
    </button>
    </form>

    <div id="error" class="text-red-500 mt-3 text-sm"></div>

    <div class="text-center mt-4 text-sm text-gray-500">
        Belum punya akun?
        <a href="/register" class="text-indigo-500 hover:underline">Daftar</a>
    </div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
        {{ session('error') }}
    </div>
@endif

@endsection