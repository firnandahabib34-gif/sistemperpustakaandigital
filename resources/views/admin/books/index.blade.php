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
<input id="search" type="text" placeholder="Cari judul buku..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Grid Buku -->
<div id="booksGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

<!-- Modal (sama seperti sebelumnya) -->
<div id="modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 id="modalTitle" class="text-lg font-bold">Tambah Buku</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Judul Buku</label>
            <input id="title" placeholder="Masukkan judul buku" class="w-full p-2 border rounded-lg mb-3">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Penulis</label>
            <input id="author" placeholder="Nama penulis" class="w-full p-2 border rounded-lg mb-3">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Stok</label>
            <input id="stock" type="number" placeholder="Jumlah stok" class="w-full p-2 border rounded-lg mb-4">
        </div>

        <div class="flex gap-2">
            <button onclick="saveBook()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg w-full hover:bg-indigo-600 transition">
                Simpan
            </button>
            <button onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg w-full hover:bg-gray-500 transition">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
let books = JSON.parse(localStorage.getItem('admin_books')) || [
    { id: 1, title: "Laravel 11", author: "Taylor Otwell", stock: 5 },
    { id: 2, title: "Tailwind CSS", author: "Adam Wathan", stock: 3 },
    { id: 3, title: "Pemrograman Web", author: "Sandhika Galih", stock: 4 },
    { id: 4, title: "Basis Data", author: "Rosa A.S.", stock: 2 }
];

let editId = null;

function saveToStorage() {
    localStorage.setItem('admin_books', JSON.stringify(books));
    localStorage.setItem('anggota_books', JSON.stringify(books.map(b => ({
        id: b.id,
        title: b.title,
        author: b.author,
        category: "Teknologi",
        stock: b.stock
    }))));
}

function renderBooks() {
    const search = document.getElementById('search').value.toLowerCase();
    const grid = document.getElementById('booksGrid');
    if (!grid) return;
    
    grid.innerHTML = "";

    const filteredBooks = books.filter(b => 
        b.title.toLowerCase().includes(search) || 
        b.author.toLowerCase().includes(search)
    );

    if (filteredBooks.length === 0) {
        grid.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-10">📚 Tidak ada buku. Silakan tambah buku baru.</div>';
        return;
    }

    filteredBooks.forEach(book => {
        grid.innerHTML += `
        <div class="bg-white p-4 rounded-xl shadow hover:shadow-lg transition">
            <h3 class="font-bold text-lg">${escapeHtml(book.title)}</h3>
            <p class="text-sm text-gray-500 mt-1"><i class="fas fa-user"></i> ${escapeHtml(book.author)}</p>
            <p class="text-sm mt-1"><i class="fas fa-boxes"></i> Stok: <span class="font-semibold">${book.stock}</span></p>

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
    document.getElementById('title').value = '';
    document.getElementById('author').value = '';
    document.getElementById('stock').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Buku';
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}

function saveBook() {
    const title = document.getElementById('title').value.trim();
    const author = document.getElementById('author').value.trim();
    const stock = parseInt(document.getElementById('stock').value);

    if (!title || !author || isNaN(stock) || stock < 0) {
        alert('Harap isi semua field dengan benar!');
        return;
    }

    if (editId) {
        let book = books.find(b => b.id === editId);
        if (book) {
            book.title = title;
            book.author = author;
            book.stock = stock;
        }
    } else {
        books.push({
            id: Date.now(),
            title: title,
            author: author,
            stock: stock
        });
    }

    saveToStorage();
    closeModal();
    renderBooks();
    alert(editId ? '✅ Buku berhasil diupdate!' : '✅ Buku berhasil ditambahkan!');
}

function editBook(id) {
    const book = books.find(b => b.id === id);
    if (!book) return;

    document.getElementById('title').value = book.title;
    document.getElementById('author').value = book.author;
    document.getElementById('stock').value = book.stock;

    editId = id;
    document.getElementById('modalTitle').innerText = 'Edit Buku';
    document.getElementById('modal').classList.remove('hidden');
}

function deleteBook(id) {
    const book = books.find(b => b.id === id);
    if (confirm(`Yakin ingin menghapus buku "${book?.title}"?`)) {
        books = books.filter(b => b.id !== id);
        saveToStorage();
        renderBooks();
        alert('🗑️ Buku berhasil dihapus!');
    }
}

document.getElementById('search').addEventListener('input', renderBooks);
renderBooks();
</script>

@endsection