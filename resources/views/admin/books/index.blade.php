@extends('layouts.app')

@section('title', 'Kelola Buku')

@section('content')

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold">Kelola Buku</h1>
        <p class="text-gray-500 text-sm">Manajemen koleksi buku perpustakaan</p>
    </div>
    <button onclick="openModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition">
        + Tambah Buku
    </button>
</div>

<!-- Search -->
<input id="search" type="text" placeholder="Cari judul buku atau penulis..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Grid Buku -->
<div id="booksGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

<!-- Modal Tambah/Edit Buku -->
<div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h2 id="modalTitle" class="text-lg font-bold">Tambah Buku</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
            <form id="bookForm">
                <input type="hidden" id="bookId" name="book_id">
                
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Judul Buku <span class="text-red-500">*</span></label>
                    <input id="judul" type="text" placeholder="Masukkan judul buku" 
                        class="w-full p-2 border rounded-lg" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Penulis <span class="text-red-500">*</span></label>
                    <input id="penulis" type="text" placeholder="Nama penulis" 
                        class="w-full p-2 border rounded-lg" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Kategori</label>
                    <select id="category_id" class="w-full p-2 border rounded-lg">
                        <option value="">Pilih Kategori</option>
                        <!-- Data kategori akan diisi JavaScript -->
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Stok <span class="text-red-500">*</span></label>
                    <input id="stok" type="number" placeholder="Jumlah stok" 
                        class="w-full p-2 border rounded-lg" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Penerbit</label>
                    <input id="penerbit" type="text" placeholder="Nama penerbit" 
                        class="w-full p-2 border rounded-lg">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tahun Terbit</label>
                    <input id="tahun" type="text" placeholder="Contoh: 2024" 
                        class="w-full p-2 border rounded-lg">
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
// Data buku dari database (dikirim dari controller)
let books = @json($books);
let editId = null;

// CSRF Token untuk Laravel
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                  document.head.querySelector('meta[name="csrf-token"]')?.content;

function renderBooks() {
    const searchValue = document.getElementById('search').value.toLowerCase();
    const grid = document.getElementById('booksGrid');
    if (!grid) return;
    
    grid.innerHTML = "";

    const filteredBooks = books.filter(b => 
        (b.judul && b.judul.toLowerCase().includes(searchValue)) || 
        (b.penulis && b.penulis.toLowerCase().includes(searchValue))
    );

    if (filteredBooks.length === 0) {
        grid.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-10">📚 Tidak ada buku. Silakan tambah buku baru.</div>';
        return;
    }

    filteredBooks.forEach(book => {
        // Ambil nama kategori dari relasi category (jika ada)
        const kategoriNama = book.category ? book.category.nama : '-';
        
        grid.innerHTML += `
            <div class="bg-white p-4 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="font-bold text-lg">${escapeHtml(book.judul)}</h3>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-user"></i> ${escapeHtml(book.penulis)}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-tag"></i> ${escapeHtml(kategoriNama)}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-building"></i> ${escapeHtml(book.penerbit) || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-calendar"></i> ${book.tahun || '-'}</p>
                <p class="text-sm mt-1"><i class="fas fa-boxes"></i> Stok: <span class="font-semibold">${book.stok}</span></p>

                <div class="mt-4 flex gap-2">
                    <button onclick="editBook(${book.id})" class="bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded text-sm transition cursor-pointer">
                        ✏️ Edit
                    </button>
                    <button onclick="deleteBook(${book.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition cursor-pointer">
                        🗑️ Hapus
                    </button>
                </div>
            </div>
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
    document.getElementById('bookId').value = '';
    document.getElementById('judul').value = '';
    document.getElementById('penulis').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('stok').value = '';
    document.getElementById('penerbit').value = '';
    document.getElementById('tahun').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Buku';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Load kategori untuk select option
async function loadCategories() {
    try {
        const response = await fetch('/api/categories');
        const categories = await response.json();
        
        const select = document.getElementById('category_id');
        select.innerHTML = '<option value="">Pilih Kategori</option>';
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.nama;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Gagal load kategori:', error);
    }
}

// Submit form (Tambah/Edit)
document.getElementById('bookForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const judul = document.getElementById('judul').value.trim();
    const penulis = document.getElementById('penulis').value.trim();
    const category_id = document.getElementById('category_id').value;
    const stok = parseInt(document.getElementById('stok').value);
    const penerbit = document.getElementById('penerbit').value.trim();
    const tahun = document.getElementById('tahun').value.trim();

    if (!judul || !penulis || isNaN(stok)) {
        alert('Harap isi Judul, Penulis, dan Stok dengan benar!');
        return;
    }

    let url, method, body;

    if (editId) {
        // UPDATE
        url = `/admin/books/${editId}`;
        method = 'PUT';
        body = { judul, penulis, category_id, stok, penerbit, tahun, _method: 'PUT' };
    } else {
        // CREATE
        url = '/admin/books';
        method = 'POST';
        body = { judul, penulis, category_id, stok, penerbit, tahun };
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
            alert(editId ? '✅ Buku berhasil diupdate!' : '✅ Buku berhasil ditambahkan!');
            closeModal();
            location.reload();
        } else {
            alert('❌ Gagal menyimpan buku: ' + (result.message || 'Terjadi kesalahan'));
        }
    } catch (error) {
        alert('❌ Terjadi kesalahan: ' + error.message);
    }
});

async function editBook(id) {
    try {
        const response = await fetch(`/admin/books/${id}/edit`);
        const book = await response.json();

        document.getElementById('bookId').value = book.id;
        document.getElementById('judul').value = book.judul;
        document.getElementById('penulis').value = book.penulis;
        document.getElementById('category_id').value = book.category_id || '';
        document.getElementById('stok').value = book.stok;
        document.getElementById('penerbit').value = book.penerbit || '';
        document.getElementById('tahun').value = book.tahun || '';

        editId = id;
        document.getElementById('modalTitle').innerText = 'Edit Buku';
        document.getElementById('modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        alert('Gagal mengambil data buku');
    }
}

async function deleteBook(id) {
    const book = books.find(b => b.id === id);
    if (confirm(`Yakin ingin menghapus buku "${book?.judul}"?`)) {
        try {
            const response = await fetch(`/admin/books/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                alert('🗑️ Buku berhasil dihapus!');
                location.reload();
            } else {
                alert('❌ Gagal menghapus buku');
            }
        } catch (error) {
            alert('❌ Terjadi kesalahan: ' + error.message);
        }
    }
}

// Event listener search
document.getElementById('search').addEventListener('input', renderBooks);

// Load kategori dan render awal
loadCategories();
renderBooks();
</script>

@endsection