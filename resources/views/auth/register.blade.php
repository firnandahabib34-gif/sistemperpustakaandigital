@extends('layouts.auth')

@section('title', 'Register')

@section('content')

<div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg">

    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Akun</h2>
        <p class="text-sm text-gray-500">Bergabung dengan perpustakaan</p>
    </div>

    <div id="error" class="hidden bg-red-100 text-red-700 text-sm p-2 rounded mb-4"></div>
    <div id="success" class="hidden bg-green-100 text-green-700 text-sm p-2 rounded mb-4"></div>

    <form id="registerForm" class="space-y-4">

        <div>
            <label class="block text-sm text-gray-700">NIM *</label>
            <input type="text" id="nim" class="w-full mt-1 p-2 border rounded-lg" required>
        </div>

        <div>
            <label class="block text-sm text-gray-700">Nama Lengkap *</label>
            <input type="text" id="name" class="w-full mt-1 p-2 border rounded-lg" required>
        </div>

        <div>
            <label class="block text-sm text-gray-700">Email</label>
            <input type="email" id="email" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div>
            <label class="block text-sm text-gray-700">No Telepon</label>
            <input type="text" id="phone" class="w-full mt-1 p-2 border rounded-lg">
        </div>

        <div>
            <label class="block text-sm text-gray-700">Program Studi</label>
            <select id="prodi" class="w-full mt-1 p-2 border rounded-lg">
                <option value="">Pilih Program Studi</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Teknik Komputer">Teknik Komputer</option>
                <option value="Manajemen Informatika">Manajemen Informatika</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-700">Password *</label>
            <input type="password" id="password" class="w-full mt-1 p-2 border rounded-lg" required>
        </div>

        <div>
            <label class="block text-sm text-gray-700">Konfirmasi Password *</label>
            <input type="password" id="password_confirmation" class="w-full mt-1 p-2 border rounded-lg" required>
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Daftar
        </button>

    </form>

    <div class="text-center mt-4 text-sm text-gray-500">
        Sudah punya akun?
        <a href="/login" class="text-indigo-500 hover:underline">Login</a>
    </div>

</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Ambil nilai dari form
    const nim = document.getElementById('nim').value.trim();
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const prodi = document.getElementById('prodi').value;
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    // Reset pesan error/success
    const errorDiv = document.getElementById('error');
    const successDiv = document.getElementById('success');
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    
    // Validasi
    if (!nim || !name || !password) {
        errorDiv.innerText = '❌ NIM, Nama, dan Password wajib diisi!';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    if (password.length < 6) {
        errorDiv.innerText = '❌ Password minimal 6 karakter!';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    if (password !== passwordConfirmation) {
        errorDiv.innerText = '❌ Konfirmasi password tidak cocok!';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    // Ambil data anggota yang sudah ada
    let anggotas = JSON.parse(localStorage.getItem('admin_anggotas')) || [];
    
    // Cek apakah NIM sudah terdaftar
    const existing = anggotas.find(a => a.nim === nim);
    if (existing) {
        errorDiv.innerText = `❌ NIM ${nim} sudah terdaftar! Silakan login.`;
        errorDiv.classList.remove('hidden');
        return;
    }
    
    // Buat ID baru
    const newId = anggotas.length > 0 ? Math.max(...anggotas.map(a => a.id)) + 1 : 1;
    
    // Tambah anggota baru
    const newAnggota = {
        id: newId,
        nim: nim,
        name: name,
        email: email,
        phone: phone,
        prodi: prodi,
        password: password,
        status: 'aktif',
        created_at: new Date().toISOString()
    };
    
    anggotas.push(newAnggota);
    
    // Simpan ke localStorage
    localStorage.setItem('admin_anggotas', JSON.stringify(anggotas));
    
    // Sinkron ke data login
    const loginAnggotas = anggotas.map(a => ({
        nim: a.nim,
        password: a.password,
        name: a.name,
        prodi: a.prodi,
        status: a.status
    }));
    localStorage.setItem('anggota_list', JSON.stringify(loginAnggotas));
    
    // Tampilkan pesan sukses
    successDiv.innerText = '✅ Pendaftaran berhasil! Mengarahkan ke halaman login...';
    successDiv.classList.remove('hidden');
    
    // Redirect ke halaman login setelah 2 detik
    setTimeout(() => {
        window.location.href = '/login';
    }, 2000);
});
</script>

@endsection