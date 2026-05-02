@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Buku</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 p-6">

<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Kelola Buku</h1>
            <p class="text-gray-500 text-sm">Tanpa backend, pakai localStorage</p>
        </div>
        <button onclick="openModal()" class="bg-indigo-500 text-white px-4 py-2 rounded-lg">
            Tambah Buku
        </button>
    </div>

    <!-- Search -->
    <input id="search" type="text" placeholder="Cari buku..."
        class="w-full mb-4 p-2 border rounded-lg">

    <!-- Grid -->
    <div id="booksGrid" class="grid md:grid-cols-3 gap-4"></div>

</div>

<!-- Modal -->
<div id="modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl w-full max-w-md">
        <h2 id="modalTitle" class="text-lg font-bold mb-4">Tambah Buku</h2>

        <input id="title" placeholder="Judul" class="w-full p-2 border rounded mb-2">
        <input id="author" placeholder="Penulis" class="w-full p-2 border rounded mb-2">
        <input id="stock" type="number" placeholder="Stok" class="w-full p-2 border rounded mb-2">

        <div class="flex gap-2">
            <button onclick="saveBook()" class="bg-indigo-500 text-white px-4 py-2 rounded w-full">
                Simpan
            </button>
            <button onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded w-full">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
let books = JSON.parse(localStorage.getItem('books')) || [
    { id: 1, title: "Laravel", author: "Budi", stock: 5 },
    { id: 2, title: "Tailwind", author: "Andi", stock: 3 }
];

let editId = null;

function saveToStorage() {
    localStorage.setItem('books', JSON.stringify(books));
}

function renderBooks() {
    const search = document.getElementById('search').value.toLowerCase();
    const grid = document.getElementById('booksGrid');
    grid.innerHTML = "";

    books
    .filter(b => b.title.toLowerCase().includes(search))
    .forEach(book => {
        grid.innerHTML += `
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-bold">${book.title}</h3>
            <p class="text-sm text-gray-500">${book.author}</p>
            <p class="text-sm">Stok: ${book.stock}</p>

            <div class="mt-3 flex gap-2">
                <button onclick="editBook(${book.id})" class="bg-yellow-400 px-2 py-1 rounded text-sm">
                    Edit
                </button>
                <button onclick="deleteBook(${book.id})" class="bg-red-500 text-white px-2 py-1 rounded text-sm">
                    Hapus
                </button>
            </div>
        </div>
        `;
    });
}

function openModal() {
    editId = null;
    clearForm();
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}

function clearForm() {
    document.getElementById('title').value = "";
    document.getElementById('author').value = "";
    document.getElementById('stock').value = "";
}

function saveBook() {
    const title = document.getElementById('title').value;
    const author = document.getElementById('author').value;
    const stock = document.getElementById('stock').value;

    if (!title || !author || !stock) return;

    if (editId) {
        let book = books.find(b => b.id === editId);
        book.title = title;
        book.author = author;
        book.stock = stock;
    } else {
        books.push({
            id: Date.now(),
            title,
            author,
            stock
        });
    }

    saveToStorage();
    closeModal();
    renderBooks();
}

function editBook(id) {
    const book = books.find(b => b.id === id);

    document.getElementById('title').value = book.title;
    document.getElementById('author').value = book.author;
    document.getElementById('stock').value = book.stock;

    editId = id;
    document.getElementById('modal').classList.remove('hidden');
}

function deleteBook(id) {
    books = books.filter(b => b.id !== id);
    saveToStorage();
    renderBooks();
}

document.getElementById('search').addEventListener('input', renderBooks);

renderBooks();
</script>

</body>
</html>
@endsection