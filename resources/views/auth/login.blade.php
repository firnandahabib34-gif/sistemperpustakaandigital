@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg">

    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Login ke E-Library</h2>
        <p class="text-sm text-gray-500">Masuk ke sistem perpustakaan</p>
    </div>
    
    <form id="loginForm" class="space-y-4">

        <div>
            <label class="block text-sm font-medium text-gray-700">NIM</label>
            <input type="text" id="nim" class="w-full mt-1 p-2 border rounded-lg" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" class="w-full mt-1 p-2 border rounded-lg" required>
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

</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const nim = document.getElementById('nim').value;
    const password = document.getElementById('password').value;

    const users = [
        { nim: '12345', password: '12345', role: 'mahasiswa' },
        { nim: 'admin', password: 'admin', role: 'admin' }
    ];

    const user = users.find(u => u.nim === nim && u.password === password);

    if (!user) {
        document.getElementById('error').innerText = 'NIM atau password salah';
        return;
    }

    if (user.role === 'admin') {
        window.location.href = '/dashboard-admin';
    } else {
        window.location.href = '/dashboard-anggota';
    }
});
</script>

@endsection