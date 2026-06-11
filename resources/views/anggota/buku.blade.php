@extends('layouts.app')

@section('title', 'Koleksi Buku')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold">Koleksi Buku</h1>
        <p class="text-gray-500 text-sm">Silakan pilih buku yang ingin dipinjam</p>
    </div>
    <button onclick="showNotifications()" class="bg-white px-4 py-2 rounded-lg shadow flex items-center gap-2 hover:bg-gray-50 transition relative">
        <i class="fas fa-bell"></i> Notifikasi
        <span id="notifBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5 min-w-[20px] text-center hidden">0</span>
    </button>
</div>

<!-- Pencarian -->
<input id="search" type="text" placeholder="Cari judul buku atau penulis..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Grid Buku -->
<div id="booksGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

<!-- Modal Peminjaman Saya -->
<div id="loansModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-md mx-4 max-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-bold">Peminjaman Saya</h2>
            <button onclick="closeLoansModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div id="loansList" class="p-4 space-y-3"></div>
    </div>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Data dari database (dikirim dari controller)
let books = @json($books);
let loans = [];
let notifications = [];

// Ambil data peminjaman dari database
async function loadLoans() {
    try {
        const response = await fetch('/api/anggota/loans');
        loans = await response.json();
        updateNotifBadge();
        renderBooks(); // Re-render setelah data loans berubah
    } catch (error) {
        console.error('Gagal load loans:', error);
    }
}

// Ambil notifikasi
async function loadNotifications() {
    try {
        const response = await fetch('/api/anggota/notifications');
        notifications = await response.json();
        updateNotifBadge();
    } catch (error) {
        console.error('Gagal load notifikasi:', error);
    }
}

function updateNotifBadge() {
    const badge = document.getElementById('notifBadge');
    if (badge) {
        const unread = notifications.length;
        badge.innerText = unread;
        if (unread === 0) {
            badge.classList.add('hidden');
        } else {
            badge.classList.remove('hidden');
        }
    }
}

function showNotifications() {
    if (notifications.length === 0) {
        alert('📭 Belum ada notifikasi.');
        return;
    }
    
    let msg = '🔔 NOTIFIKASI\n\n';
    notifications.forEach(n => {
        msg += `📌 ${n.message}\n   📅 ${new Date(n.created_at).toLocaleString()}\n\n`;
    });
    alert(msg);
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

// Fungsi pinjam buku via AJAX
async function pinjamBuku(bookId) {
    console.log('Tombol diklik, bookId:', bookId); // Cek apakah fungsi terpanggil
    
    try {
        const response = await fetch(`/pinjam/${bookId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({})
        });

        const result = await response.json();
        console.log('Response:', result); // Cek response dari server

        if (response.ok) {
            alert('✅ ' + result.message);
            location.reload(); // Refresh halaman
        } else {
            alert('❌ ' + (result.message || 'Gagal meminjam buku'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan: ' + error.message);
    }
}
// Ambil data buku terbaru
async function loadBooks() {
    try {
        const response = await fetch('/api/books');
        books = await response.json();
        renderBooks();
    } catch (error) {
        console.error('Gagal load buku:', error);
    }
}

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
        grid.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-10">📚 Tidak ada buku yang ditemukan.</div>';
        return;
    }

    filteredBooks.forEach(book => {
        const alreadyBorrowed = loans.some(l => l.book_id === book.id && (l.status === 'dipinjam' || l.status === 'menunggu'));
        const available = book.stok > 0 && !alreadyBorrowed;
        const kategoriNama = book.category ? book.category.nama : '-';
        
        grid.innerHTML += `
            <div class="bg-white p-4 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="font-bold text-lg">${escapeHtml(book.judul)}</h3>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-user"></i> ${escapeHtml(book.penulis)}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-tag"></i> ${escapeHtml(kategoriNama)}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-building"></i> ${escapeHtml(book.penerbit) || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-calendar"></i> ${book.tahun || '-'}</p>
                <p class="text-sm mt-1"><i class="fas fa-boxes"></i> Stok: <span class="font-semibold">${book.stok}</span></p>
                
                <button onclick="pinjamBuku(${book.id})" 
                    class="w-full mt-4 ${available ? 'bg-indigo-500 hover:bg-indigo-600' : 'bg-gray-400 cursor-not-allowed'} text-white py-2 rounded-lg transition"
                    ${!available ? 'disabled' : ''}>
                    ${alreadyBorrowed ? '📌 Sedang Dipinjam/Menunggu' : (book.stok > 0 ? '📖 Ajukan Peminjaman' : '❌ Stok Habis')}
                </button>
            </div>
        `;
    });
}

// Event listener search
document.getElementById('search').addEventListener('input', renderBooks);

// Fungsi untuk modal peminjaman saya
function showMyLoans() {
    const modal = document.getElementById('loansModal');
    const loansList = document.getElementById('loansList');
    
    if (!modal || !loansList) return;
    
    if (loans.length === 0) {
        loansList.innerHTML = '<p class="text-center text-gray-500 py-4">📭 Belum ada peminjaman</p>';
    } else {
        loansList.innerHTML = loans.map(loan => {
            const statusClass = {
                'menunggu': 'bg-yellow-100 text-yellow-700',
                'dipinjam': 'bg-blue-100 text-blue-700',
                'dikembalikan': 'bg-green-100 text-green-700',
                'ditolak': 'bg-red-100 text-red-700'
            }[loan.status] || 'bg-gray-100 text-gray-700';
            
            const statusText = {
                'menunggu': 'Menunggu',
                'dipinjam': 'Dipinjam',
                'dikembalikan': 'Dikembalikan',
                'ditolak': 'Ditolak'
            }[loan.status] || loan.status;
            
            return `
                <div class="border p-3 rounded-lg">
                    <div class="font-bold">📖 ${loan.book ? loan.book.judul : '-'}</div>
                    <div class="text-sm text-gray-500 mt-1">📅 Pinjam: ${new Date(loan.borrow_date).toLocaleDateString('id-ID')}</div>
                    <div class="text-sm text-gray-500">⏰ Jatuh tempo: ${new Date(loan.due_date).toLocaleDateString('id-ID')}</div>
                    <div class="text-sm mt-2">
                        <span class="px-2 py-1 rounded-full text-xs ${statusClass}">${statusText}</span>
                    </div>
                    ${loan.fine > 0 ? `<div class="text-sm text-red-600 mt-1">💰 Denda: Rp ${loan.fine.toLocaleString('id-ID')}</div>` : ''}
                </div>
            `;
        }).join('');
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLoansModal() {
    const modal = document.getElementById('loansModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Event listener untuk sidebar
window.addEventListener('showMyLoans', function() {
    showMyLoans();
});

// Load data awal
loadBooks();
loadLoans();
loadNotifications();

// Refresh data setiap 5 detik
setInterval(() => {
    loadLoans();
    loadBooks();
}, 5000);
</script>

@endsection