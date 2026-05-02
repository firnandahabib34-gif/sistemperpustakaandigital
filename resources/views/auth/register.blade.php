@extends('layouts.auth')

@section('title', 'Register')

@section('content')

<div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg">

    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Akun</h2>
        <p class="text-sm text-gray-500">Bergabung dengan perpustakaan</p>
    </div>

    <form class="space-y-4">

        <div>
            <label class="block text-sm text-gray-700">NIM</label>
            <input type="text" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div>
            <label class="block text-sm text-gray-700">Nama</label>
            <input type="text" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div>
            <label class="block text-sm text-gray-700">Email</label>
            <input type="email" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div>
            <label class="block text-sm text-gray-700">No Telepon</label>
            <input type="text" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div>
            <label class="block text-sm text-gray-700">Program Studi</label>
            <input type="text" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div>
            <label class="block text-sm text-gray-700">Password</label>
            <input type="password" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <button class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Daftar
        </button>

    </form>

    <div class="text-center mt-4 text-sm text-gray-500">
        Sudah punya akun?
        <a href="/login" class="text-indigo-500 hover:underline">Login</a>
    </div>

</div>

@endsection