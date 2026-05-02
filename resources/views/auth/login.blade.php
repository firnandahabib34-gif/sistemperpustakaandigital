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

    // Ambil data anggota dari localStorage
    const anggotaList = JSON.parse(localStorage.getItem('anggota_list')) || [];
    const user = anggotaList.find(u => u.nim === nim && u.password === password);
    
    // Cek admin (hardcoded)
    const admin = (nim === 'admin' && password === 'admin');

    if (!user && !admin) {
        document.getElementById('error').innerText = 'NIM atau password salah';
        return;
    }

    // Cek status anggota (nonaktif tidak boleh login)
    if (user && user.status === 'nonaktif') {
        document.getElementById('error').innerText = 'Akun Anda sedang nonaktif. Hubungi admin.';
        return;
    }

    // Simpan sesi login
    if (admin) {
        localStorage.setItem('logged_in', JSON.stringify({ nim: 'admin', role: 'admin' }));
        window.location.href = '/dashboard-admin';
    } else {
        localStorage.setItem('logged_in', JSON.stringify({ nim: user.nim, role: 'anggota', name: user.name }));
        window.location.href = '/dashboard-anggota';
    }
});
</script>

@endsection