@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold">Kelola Kategori</h1>
        <p class="text-gray-500 text-sm">Manajemen kategori buku perpustakaan</p>
    </div>
    <button onclick="openModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition">
        + Tambah Kategori
    </button>
</div>

<!-- Search -->
<input id="search" type="text" placeholder="Cari nama kategori..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Tabel Kategori -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-600">ID</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Nama Kategori</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Deskripsi</th>
                    <th class="text-center p-4 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody id="kategoriTable">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Kategori -->
<div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h2 id="modalTitle" class="text-lg font-bold">Tambah Kategori</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Kategori *</label>
                <input id="nama" type="text" placeholder="Contoh: Teknologi, Fiksi, Matematika" 
                    class="w-full p-2 border rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <textarea id="deskripsi" rows="3" placeholder="Deskripsi kategori (opsional)" 
                    class="w-full p-2 border rounded-lg"></textarea>
            </div>

            <div class="flex gap-2">
                <button onclick="saveKategori()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg w-full hover:bg-indigo-600 transition">
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
// Data kategori
let kategoris = JSON.parse(localStorage.getItem('admin_kategoris')) || [
    { id: 1, nama: "Teknologi", deskripsi: "Buku tentang teknologi dan programming" },
    { id: 2, nama: "Matematika", deskripsi: "Buku matematika dasar hingga lanjut" },
    { id: 3, nama: "Fisika", deskripsi: "Buku fisika dan ilmu alam" },
    { id: 4, nama: "Kimia", deskripsi: "Buku kimia dasar dan praktikum" },
    { id: 5, nama: "Bahasa", deskripsi: "Buku bahasa Indonesia dan Inggris" }
];

let editId = null;

function saveToStorage() {
    localStorage.setItem('admin_kategoris', JSON.stringify(kategoris));
    // Sinkron ke pilihan kategori di form buku
    syncKategoriToBooks();
}

function syncKategoriToBooks() {
    const kategoriOptions = kategoris.map(k => k.nama);
    localStorage.setItem('kategori_options', JSON.stringify(kategoriOptions));
}

function renderKategoris() {
    const search = document.getElementById('search').value.toLowerCase();
    const tbody = document.getElementById('kategoriTable');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    const filtered = kategoris.filter(k => 
        k.nama.toLowerCase().includes(search)
    );

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-gray-500 py-10">
                    📭 Tidak ada data kategori. Silakan tambah kategori baru.
                </td>
            </tr>
        `;
        return;
    }

    filtered.forEach(kategori => {
        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4">${kategori.id}</td>
                <td class="p-4 font-medium">${escapeHtml(kategori.nama)}</td>
                <td class="p-4 text-gray-600">${kategori.deskripsi || '-'}</td>
                <td class="p-4 text-center whitespace-nowrap">
                    <button onclick="editKategori(${kategori.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs mr-1">
                        Edit
                    </button>
                    <button onclick="deleteKategori(${kategori.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
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
    document.getElementById('nama').value = '';
    document.getElementById('deskripsi').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Kategori';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function saveKategori() {
    const nama = document.getElementById('nama').value.trim();
    const deskripsi = document.getElementById('deskripsi').value.trim();

    if (!nama) {
        alert('Nama kategori wajib diisi!');
        return;
    }

    if (editId) {
        let kategori = kategoris.find(k => k.id === editId);
        if (kategori) {
            kategori.nama = nama;
            kategori.deskripsi = deskripsi;
        }
    } else {
        // Cek duplikat
        const existing = kategoris.find(k => k.nama.toLowerCase() === nama.toLowerCase());
        if (existing) {
            alert(`Kategori "${nama}" sudah ada!`);
            return;
        }
        
        kategoris.push({
            id: Date.now(),
            nama: nama,
            deskripsi: deskripsi
        });
    }

    saveToStorage();
    closeModal();
    renderKategoris();
    alert(editId ? '✅ Kategori berhasil diupdate!' : '✅ Kategori berhasil ditambahkan!');
}

function editKategori(id) {
    const kategori = kategoris.find(k => k.id === id);
    if (!kategori) return;

    document.getElementById('nama').value = kategori.nama;
    document.getElementById('deskripsi').value = kategori.deskripsi || '';

    editId = id;
    document.getElementById('modalTitle').innerText = 'Edit Kategori';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function deleteKategori(id) {
    const kategori = kategoris.find(k => k.id === id);
    if (confirm(`Yakin ingin menghapus kategori "${kategori?.nama}"?`)) {
        kategoris = kategoris.filter(k => k.id !== id);
        saveToStorage();
        renderKategoris();
        alert('🗑️ Kategori berhasil dihapus!');
    }
}

// Search event
document.getElementById('search').addEventListener('input', renderKategoris);

// Initial render
syncKategoriToBooks();
renderKategoris();
</script>

@endsection