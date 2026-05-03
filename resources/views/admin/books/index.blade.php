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
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Judul Buku <span class="text-red-500">*</span></label>
                <input id="title" placeholder="Masukkan judul buku" class="w-full p-2 border rounded-lg">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Penulis <span class="text-red-500">*</span></label>
                <input id="author" placeholder="Nama penulis" class="w-full p-2 border rounded-lg">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <select id="category" class="w-full p-2 border rounded-lg">
                    <option value="">Pilih Kategori</option>
                    <!-- Data kategori akan diisi oleh JavaScript -->
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Stok <span class="text-red-500">*</span></label>
                <input id="stock" type="number" placeholder="Jumlah stok" class="w-full p-2 border rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tahun Terbit</label>
                <input id="year" type="text" placeholder="Contoh: 2024" class="w-full p-2 border rounded-lg">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Penerbit</label>
                <input id="penerbit" type="text" placeholder="Nama penerbit" class="w-full p-2 border rounded-lg">
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
</div>

<script>
// Data buku
let books = JSON.parse(localStorage.getItem('admin_books')) || [
    { id: 1, title: "Laravel 11", author: "Taylor Otwell", category: "Teknologi", stock: 5, year: 2024, penerbit: "O'Reilly Media" },
    { id: 2, title: "Tailwind CSS", author: "Adam Wathan", category: "Teknologi", stock: 3, year: 2023, penerbit: "Tailwind Labs" },
    { id: 3, title: "Pemrograman Web", author: "Sandhika Galih", category: "Teknologi", stock: 4, year: 2024, penerbit: "UNPAS Press" },
    { id: 4, title: "Basis Data", author: "Rosa A.S.", category: "Teknologi", stock: 2, year: 2023, penerbit: "Informatika" }
];

let editId = null;

// Load kategori dari localStorage ke select option
function loadKategoriOptions() {
    const kategoris = JSON.parse(localStorage.getItem('admin_kategoris')) || [
        { id: 1, nama: "Teknologi" },
        { id: 2, nama: "Matematika" },
        { id: 3, nama: "Fisika" },
        { id: 4, nama: "Kimia" },
        { id: 5, nama: "Bahasa" }
    ];
    
    const select = document.getElementById('category');
    if (select) {
        select.innerHTML = '<option value="">Pilih Kategori</option>';
        kategoris.forEach(k => {
            select.innerHTML += `<option value="${k.nama}">${k.nama}</option>`;
        });
    }
}

function saveToStorage() {
    localStorage.setItem('admin_books', JSON.stringify(books));
    // Sinkron ke anggota books
    localStorage.setItem('anggota_books', JSON.stringify(books.map(b => ({
        id: b.id,
        title: b.title,
        author: b.author,
        category: b.category || "Teknologi",
        stock: b.stock,
        year: b.year,
        penerbit: b.penerbit 
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
    <p class="text-sm text-gray-500 mt-1"><i class="fas fa-tag"></i> ${book.category || '-'}</p>
    <p class="text-sm text-gray-500 mt-1"><i class="fas fa-building"></i> ${book.penerbit || '-'}</p>  
    <p class="text-sm mt-1"><i class="fas fa-boxes"></i> Stok: <span class="font-semibold">${book.stock}</span></p>
    ${book.year ? `<p class="text-sm text-gray-500"><i class="fas fa-calendar"></i> ${book.year}</p>` : ''}

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
    document.getElementById('category').value = '';
    document.getElementById('stock').value = '';
    document.getElementById('year').value = '';
    document.getElementById('penerbit').value = '';  // tambah ini
    document.getElementById('modalTitle').innerText = 'Tambah Buku';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function saveBook() {
    const title = document.getElementById('title').value.trim();
    const author = document.getElementById('author').value.trim();
    const category = document.getElementById('category').value;
    const stock = parseInt(document.getElementById('stock').value);
    const year = document.getElementById('year').value.trim();
    const penerbit = document.getElementById('penerbit').value.trim();  // tambah ini

    if (!title || !author || isNaN(stock) || stock < 0) {
        alert('Harap isi Judul, Penulis, dan Stok dengan benar!');
        return;
    }

    if (editId) {
        let book = books.find(b => b.id === editId);
        if (book) {
            book.title = title;
            book.author = author;
            book.category = category;
            book.stock = stock;
            book.year = year;
            book.penerbit = penerbit;  // tambah ini
        }
    } else {
        books.push({
            id: Date.now(),
            title: title,
            author: author,
            category: category || "Teknologi",
            stock: stock,
            year: year,
            penerbit: penerbit  // tambah ini
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
    document.getElementById('category').value = book.category || '';
    document.getElementById('stock').value = book.stock;
    document.getElementById('year').value = book.year || '';
    document.getElementById('penerbit').value = book.penerbit || '';  // tambah ini

    editId = id;
    document.getElementById('modalTitle').innerText = 'Edit Buku';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
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

// Event listener search
document.getElementById('search').addEventListener('input', renderBooks);

// Load kategori options saat halaman dimuat
loadKategoriOptions();

// Initial render
renderBooks();
</script>

@endsection