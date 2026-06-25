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
            <form id="bookForm" enctype="multipart/form-data">
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

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Tahun Terbit</label>
                    <input id="tahun" type="text" placeholder="Contoh: 2024" 
                        class="w-full p-2 border rounded-lg">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">ISBN</label>
                    <input id="isbn" type="text" placeholder="Contoh: 978-602-04-1234-5" 
                        class="w-full p-2 border rounded-lg">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Lokasi Rak</label>
                    <input id="lokasi_rak" type="text" placeholder="Contoh: A2-03" 
                        class="w-full p-2 border rounded-lg">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Deskripsi / Sinopsis</label>
                    <textarea id="deskripsi" rows="3" placeholder="Sinopsis buku..." 
                        class="w-full p-2 border rounded-lg"></textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Jumlah Halaman</label>
                    <input id="jumlah_halaman" type="number" placeholder="Contoh: 250" 
                        class="w-full p-2 border rounded-lg">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Sampul Buku</label>
                    <input id="sampul" type="file" accept="image/*" 
                        class="w-full p-2 border rounded-lg">
                    <div id="preview_sampul" class="mt-2 hidden">
                        <img id="preview_img" src="" class="w-24 h-32 object-cover rounded border">
                    </div>
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
// Data buku dari database
let books = @json($books);
let editId = null;

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// ============================================================
// RENDER BUKU
// ============================================================
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
        const kategoriNama = book.category ? book.category.nama : '-';
        
            grid.innerHTML += `
        <div class="bg-white p-2 rounded-xl shadow hover:shadow-lg transition flex gap-2">
            <!-- Info Buku (di kiri) -->
            <div class="flex-1">
                <h3 class="font-bold text-lg">${escapeHtml(book.judul)}</h3>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-user"></i> ${escapeHtml(book.penulis)}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-tag"></i> ${escapeHtml(kategoriNama)}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-building"></i> ${escapeHtml(book.penerbit) || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-calendar"></i> ${book.tahun || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-barcode"></i> ISBN: ${book.isbn || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-map-pin"></i> Rak: ${book.lokasi_rak || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-file-alt"></i> Halaman: ${book.jumlah_halaman || '-'}</p>
                <p class="text-sm mt-1"><i class="fas fa-boxes"></i> Stok: <span class="font-semibold">${book.stok}</span></p>

                <div class="mt-3 flex gap-2">
                    <button onclick="editBook(${book.id})" class="bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded text-sm transition cursor-pointer">
                        ✏️ Edit
                    </button>
                    <button onclick="deleteBook(${book.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition cursor-pointer">
                        🗑️ Hapus
                    </button>
                </div>
            </div>

            <!-- Sampul (di kanan) -->
            <div class="flex-shrink-0">
                ${book.sampul ? 
                    `<img src="/${book.sampul}" class="w-40 h-60 object-cover rounded-lg border">` : 
                    `<div class="w-24 h-32 bg-gray-200 rounded-lg border flex items-center justify-center text-gray-400 text-xs">No Cover</div>`
                }
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

// ============================================================
// MODAL
// ============================================================
function openModal() {
    editId = null;
    document.getElementById('bookId').value = '';
    document.getElementById('judul').value = '';
    document.getElementById('penulis').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('stok').value = '';
    document.getElementById('penerbit').value = '';
    document.getElementById('tahun').value = '';
    document.getElementById('isbn').value = '';
    document.getElementById('lokasi_rak').value = '';
    document.getElementById('deskripsi').value = '';
    document.getElementById('jumlah_halaman').value = '';
    document.getElementById('sampul').value = '';
    document.getElementById('preview_sampul').classList.add('hidden');
    document.getElementById('modalTitle').innerText = 'Tambah Buku';
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// ============================================================
// LOAD KATEGORI
// ============================================================
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

// ============================================================
// PREVIEW SAMPUL
// ============================================================
document.getElementById('sampul')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_img').src = e.target.result;
            document.getElementById('preview_sampul').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('preview_sampul').classList.add('hidden');
    }
});

// ============================================================
// SUBMIT FORM
// ============================================================
document.getElementById('bookForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const judul = document.getElementById('judul').value.trim();
    const penulis = document.getElementById('penulis').value.trim();
    const category_id = document.getElementById('category_id').value;
    const stok = parseInt(document.getElementById('stok').value);
    const penerbit = document.getElementById('penerbit').value.trim();
    const tahun = document.getElementById('tahun').value.trim();
    const isbn = document.getElementById('isbn').value.trim();
    const lokasi_rak = document.getElementById('lokasi_rak').value.trim();
    const deskripsi = document.getElementById('deskripsi').value.trim();
    const jumlah_halaman = document.getElementById('jumlah_halaman').value.trim();
    const sampul = document.getElementById('sampul').files[0];

    if (!judul || !penulis || isNaN(stok)) {
        alert('Harap isi Judul, Penulis, dan Stok dengan benar!');
        return;
    }

    const formData = new FormData();
    formData.append('judul', judul);
    formData.append('penulis', penulis);
    formData.append('category_id', category_id);
    formData.append('stok', stok);
    formData.append('penerbit', penerbit);
    formData.append('tahun', tahun);
    formData.append('isbn', isbn);
    formData.append('lokasi_rak', lokasi_rak);
    formData.append('deskripsi', deskripsi);
    formData.append('jumlah_halaman', jumlah_halaman);
    if (sampul) {
        formData.append('sampul', sampul);
    }

    let url, method;
    if (editId) {
        url = `/admin/books/${editId}`;
        method = 'POST';
        formData.append('_method', 'PUT');
    } else {
        url = '/admin/books';
        method = 'POST';
    }

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
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

// ============================================================
// EDIT BUKU
// ============================================================
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
        document.getElementById('isbn').value = book.isbn || '';
        document.getElementById('lokasi_rak').value = book.lokasi_rak || '';
        document.getElementById('deskripsi').value = book.deskripsi || '';
        document.getElementById('jumlah_halaman').value = book.jumlah_halaman || '';
        
        if (book.sampul) {
            document.getElementById('preview_img').src = '/' + book.sampul;
            document.getElementById('preview_sampul').classList.remove('hidden');
        } else {
            document.getElementById('preview_sampul').classList.add('hidden');
        }

        editId = id;
        document.getElementById('modalTitle').innerText = 'Edit Buku';
        document.getElementById('modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        alert('Gagal mengambil data buku');
    }
}

// ============================================================
// DELETE BUKU
// ============================================================
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

// ============================================================
// EVENT LISTENER & INITIAL LOAD
// ============================================================
document.getElementById('search').addEventListener('input', renderBooks);

loadCategories();
renderBooks();
</script>

@endsection