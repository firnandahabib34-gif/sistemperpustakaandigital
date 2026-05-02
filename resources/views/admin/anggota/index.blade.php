@extends('layouts.app')

@section('title', 'Kelola Anggota')

@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Kelola Anggota</h1>
        <p class="text-gray-500 text-sm">Manajemen data anggota perpustakaan</p>
    </div>
    <button onclick="openModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition whitespace-nowrap">
        + Tambah Anggota
    </button>
</div>

<!-- Search -->
<input id="search" type="text" placeholder="Cari NIM atau nama anggota..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Tabel Anggota - Full Width -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-600">NIM</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Nama Lengkap</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Program Studi</th>
                    <th class="text-left p-4 font-semibold text-gray-600">No. Telepon</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Email</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Status</th>
                    <th class="text-center p-4 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody id="anggotaTable">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Anggota (sama seperti form register) -->
<div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h2 id="modalTitle" class="text-lg font-bold">Tambah Anggota</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
            <div>
                <label class="block text-sm font-medium mb-1">NIM *</label>
                <input id="nim" placeholder="Contoh: 20230001" class="w-full p-2 border rounded-lg mb-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap *</label>
                <input id="name" placeholder="Nama lengkap anggota" class="w-full p-2 border rounded-lg mb-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input id="email" type="email" placeholder="contoh@email.com" class="w-full p-2 border rounded-lg mb-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">No. Telepon</label>
                <input id="phone" placeholder="Contoh: 08123456789" class="w-full p-2 border rounded-lg mb-3">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Program Studi</label>
                <select id="prodi" class="w-full p-2 border rounded-lg mb-3">
                    <option value="">Pilih Program Studi</option>
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Sistem Informasi">Sistem Informasi</option>
                    <option value="Teknik Komputer">Teknik Komputer</option>
                    <option value="Manajemen Informatika">Manajemen Informatika</option>
                    <option value="Teknik Elektro">Teknik Elektro</option>
                    <option value="Akuntansi">Akuntansi</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Password *</label>
                <input id="password" type="password" placeholder="Minimal 6 karakter" class="w-full p-2 border rounded-lg mb-4">
            </div>

            <div class="flex gap-2">
                <button onclick="saveAnggota()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg w-full hover:bg-indigo-600 transition">
                    Simpan
                </button>
                <button onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg w-full hover:bg-gray-500 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Data anggota
let anggotas = JSON.parse(localStorage.getItem('admin_anggotas')) || [
    { id: 1, nim: "20230001", name: "Budi Santoso", prodi: "Teknik Informatika", phone: "08123456789", email: "budi@email.com", password: "123456", status: "aktif" },
    { id: 2, nim: "20230002", name: "Siti Aminah", prodi: "Sistem Informasi", phone: "08123456788", email: "siti@email.com", password: "123456", status: "aktif" },
    { id: 3, nim: "20230003", name: "Andi Wijaya", prodi: "Teknik Komputer", phone: "08123456787", email: "andi@email.com", password: "123456", status: "nonaktif" }
];

let editId = null;

function saveToStorage() {
    localStorage.setItem('admin_anggotas', JSON.stringify(anggotas));
    // Sinkron ke data login anggota
    const loginAnggotas = anggotas.map(a => ({
        nim: a.nim,
        password: a.password,
        name: a.name,
        prodi: a.prodi,
        status: a.status
    }));
    localStorage.setItem('anggota_list', JSON.stringify(loginAnggotas));
}

function renderAnggotas() {
    const search = document.getElementById('search').value.toLowerCase();
    const tbody = document.getElementById('anggotaTable');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    const filtered = anggotas.filter(a => 
        a.nim.toLowerCase().includes(search) || 
        a.name.toLowerCase().includes(search)
    );

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-gray-500 py-10">
                    📭 Tidak ada data anggota. Silakan tambah anggota baru.
                </td>
            </tr>
        `;
        return;
    }

    filtered.forEach(anggota => {
        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 font-mono text-sm">${anggota.nim}</td>
                <td class="p-4 font-medium">${escapeHtml(anggota.name)}</td>
                <td class="p-4 text-gray-600">${anggota.prodi || '-'}</td>
                <td class="p-4 text-gray-600">${anggota.phone || '-'}</td>
                <td class="p-4 text-gray-600">${anggota.email || '-'}</td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded-full text-xs ${anggota.status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                        ${anggota.status === 'aktif' ? 'Aktif' : 'Nonaktif'}
                    </span>
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                    <button onclick="toggleStatus(${anggota.id})" class="bg-yellow-400 hover:bg-yellow-500 px-2 py-1 rounded text-xs mr-1">
                        ${anggota.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'}
                    </button>
                    <button onclick="editAnggota(${anggota.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs mr-1">
                        Edit
                    </button>
                    <button onclick="deleteAnggota(${anggota.id})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                        Hapus
                    </button>
                </td>
            </tr>
        `;
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function openModal() {
    editId = null;
    document.getElementById('nim').value = '';
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('prodi').value = '';
    document.getElementById('password').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Anggota';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function saveAnggota() {
    const nim = document.getElementById('nim').value.trim();
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const prodi = document.getElementById('prodi').value;
    const password = document.getElementById('password').value;

    if (!nim || !name || !password) {
        alert('Harap isi NIM, Nama, dan Password!');
        return;
    }

    if (password.length < 6) {
        alert('Password minimal 6 karakter!');
        return;
    }

    // Cek duplikat NIM (kecuali sedang edit)
    if (!editId) {
        const existing = anggotas.find(a => a.nim === nim);
        if (existing) {
            alert(`NIM ${nim} sudah terdaftar!`);
            return;
        }
    }

    if (editId) {
        let anggota = anggotas.find(a => a.id === editId);
        if (anggota) {
            anggota.nim = nim;
            anggota.name = name;
            anggota.email = email;
            anggota.phone = phone;
            anggota.prodi = prodi;
            if (password) anggota.password = password;
        }
    } else {
        anggotas.push({
            id: Date.now(),
            nim: nim,
            name: name,
            email: email,
            phone: phone,
            prodi: prodi,
            password: password,
            status: 'aktif'
        });
    }

    saveToStorage();
    closeModal();
    renderAnggotas();
    alert(editId ? '✅ Anggota berhasil diupdate!' : '✅ Anggota berhasil ditambahkan!');
}

function editAnggota(id) {
    const anggota = anggotas.find(a => a.id === id);
    if (!anggota) return;

    document.getElementById('nim').value = anggota.nim;
    document.getElementById('name').value = anggota.name;
    document.getElementById('email').value = anggota.email || '';
    document.getElementById('phone').value = anggota.phone || '';
    document.getElementById('prodi').value = anggota.prodi || '';
    document.getElementById('password').value = '';
    document.getElementById('password').placeholder = 'Kosongkan jika tidak diubah';

    editId = id;
    document.getElementById('modalTitle').innerText = 'Edit Anggota';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function deleteAnggota(id) {
    const anggota = anggotas.find(a => a.id === id);
    if (confirm(`Yakin ingin menghapus anggota "${anggota?.name}"?`)) {
        anggotas = anggotas.filter(a => a.id !== id);
        saveToStorage();
        renderAnggotas();
        alert('🗑️ Anggota berhasil dihapus!');
    }
}

function toggleStatus(id) {
    const anggota = anggotas.find(a => a.id === id);
    if (anggota) {
        anggota.status = anggota.status === 'aktif' ? 'nonaktif' : 'aktif';
        saveToStorage();
        renderAnggotas();
        alert(`Status anggota "${anggota.name}" menjadi ${anggota.status === 'aktif' ? 'Aktif' : 'Nonaktif'}`);
    }
}

// Search event
const searchInput = document.getElementById('search');
if (searchInput) {
    searchInput.addEventListener('input', renderAnggotas);
}

// Initial render
renderAnggotas();
</script>

@endsection