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
            <form id="kategoriForm">
                <input type="hidden" id="kategoriId" name="kategori_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nama Kategori *</label>
                    <input id="nama" type="text" placeholder="Contoh: Teknologi, Fiksi, Matematika" 
                        class="w-full p-2 border rounded-lg" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea id="deskripsi" rows="3" placeholder="Deskripsi kategori (opsional)" 
                        class="w-full p-2 border rounded-lg"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-lg w-full hover:bg-indigo-600 transition">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg w-full hover:bg-gray-500 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let categories = [];
let editId = null;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Load data kategori dari database
async function loadCategories() {
    try {
        const response = await fetch("{{ url('api/categories') }}");
        categories = await response.json();
        renderKategori();
    } catch (error) {
        console.error('Gagal load kategori:', error);
    }
}

function renderKategori() {
    const search = document.getElementById('search').value.toLowerCase();
    const tbody = document.getElementById('kategoriTable');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    const filtered = categories.filter(k => 
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
    document.getElementById('kategoriId').value = '';
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

// Submit form
document.getElementById('kategoriForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const nama = document.getElementById('nama').value.trim();
    const deskripsi = document.getElementById('deskripsi').value.trim();

    if (!nama) {
        alert('Nama kategori wajib diisi!');
        return;
    }

    let url, method, body;

    if (editId) {
        url = "{{ url('admin/kategori') }}/" + editId;
        method = 'PUT';
        body = { nama, deskripsi, _method: 'PUT' };
    } else {
        url = "{{ url('admin/kategori') }}";
        method = 'POST';
        body = { nama, deskripsi };
    }

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(body)
        });

        const result = await response.json();

        if (response.ok) {
            alert(editId ? '✅ Kategori berhasil diupdate!' : '✅ Kategori berhasil ditambahkan!');
            closeModal();
            loadCategories();
        } else {
            alert('❌ Gagal menyimpan kategori: ' + (result.message || 'Terjadi kesalahan'));
        }
    } catch (error) {
        alert('❌ Terjadi kesalahan: ' + error.message);
    }
});

async function editKategori(id) {
    try {
        const response = await fetch("{{ url('admin/kategori') }}/" + id + "/edit");
        const kategori = await response.json();

        document.getElementById('kategoriId').value = kategori.id;
        document.getElementById('nama').value = kategori.nama;
        document.getElementById('deskripsi').value = kategori.deskripsi || '';

        editId = id;
        document.getElementById('modalTitle').innerText = 'Edit Kategori';
        document.getElementById('modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        alert('Gagal mengambil data kategori');
    }
}

async function deleteKategori(id) {
    const kategori = categories.find(k => k.id === id);
    if (confirm(`Yakin ingin menghapus kategori "${kategori?.nama}"?`)) {
        try {
            const response = await fetch("{{ url('admin/kategori') }}/" + id, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                alert('🗑️ Kategori berhasil dihapus!');
                loadCategories();
            } else {
                alert('❌ Gagal menghapus kategori');
            }
        } catch (error) {
            alert('❌ Terjadi kesalahan: ' + error.message);
        }
    }
}

// Search event
document.getElementById('search').addEventListener('input', renderKategori);

// Load data awal
loadCategories();
</script>

@endsection