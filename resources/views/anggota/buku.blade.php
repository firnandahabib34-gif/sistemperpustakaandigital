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

<!-- Modal Detail Buku -->
<div id="detailModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-bold" id="detailTitle">Detail Buku</h2>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div id="detailContent" class="p-6">
            <!-- Konten akan diisi oleh JavaScript -->
        </div>
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
        const response = await fetch("{{ url('api/anggota/loans') }}");
        loans = await response.json();
        updateNotifBadge();
        renderBooks();
    } catch (error) {
        console.error('Gagal load loans:', error);
    }
}

// Ambil notifikasi
async function loadNotifications() {
    try {
        const response = await fetch("{{ url('api/anggota/notifications') }}");
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

// Fungsi untuk menampilkan detail buku
function showDetail(bookId) {
    const book = books.find(b => b.id === bookId);
    if (!book) {
        alert('Buku tidak ditemukan');
        return;
    }
    
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    const title = document.getElementById('detailTitle');
    
    title.textContent = `📖 ${book.judul}`;
    
    const kategoriNama = book.category ? book.category.nama : '-';
    const deskripsi = book.deskripsi || 'Tidak ada deskripsi untuk buku ini.';
    
    content.innerHTML = `
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sampul Buku -->
            <div class="flex-shrink-0">
                ${book.sampul ? 
                    `<img src="{{ url('') }}/${book.sampul}" class="w-full md:w-48 h-64 object-cover rounded-lg border shadow-md">` : 
                    `<div class="w-full md:w-48 h-64 bg-gray-200 rounded-lg border flex items-center justify-center text-gray-400 text-sm">Tidak ada sampul</div>`
                }
            </div>
            
            <!-- Informasi Buku -->
            <div class="flex-1 space-y-3">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">${escapeHtml(book.judul)}</h3>
                    <p class="text-gray-600">oleh ${escapeHtml(book.penulis)}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><span class="font-semibold">Kode Buku:</span> ${book.kode_buku || '-'}</div>
                    <div><span class="font-semibold">Kategori:</span> ${escapeHtml(kategoriNama)}</div>
                    <div><span class="font-semibold">Penerbit:</span> ${escapeHtml(book.penerbit) || '-'}</div>
                    <div><span class="font-semibold">Tahun:</span> ${book.tahun || '-'}</div>
                    <div><span class="font-semibold">ISBN:</span> ${book.isbn || '-'}</div>
                    <div><span class="font-semibold">Lokasi Rak:</span> ${book.lokasi_rak || '-'}</div>
                    <div><span class="font-semibold">Halaman:</span> ${book.jumlah_halaman || '-'}</div>
                    <div><span class="font-semibold">Stok:</span> <span class="font-bold ${book.stok > 0 ? 'text-green-600' : 'text-red-600'}">${book.stok}</span></div>
                </div>
                
                <!-- Deskripsi -->
                <div class="mt-4">
                    <h4 class="font-semibold text-gray-700 mb-2">📝 Deskripsi</h4>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-48 overflow-y-auto">
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">${escapeHtml(deskripsi)}</p>
                    </div>
                </div>
                
                <!-- Tombol Aksi -->
                <div class="flex gap-3 mt-4">
                    <button onclick="closeDetailModal()" 
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                        Tutup
                    </button>
                    ${book.stok > 0 ? 
                        `<button onclick="pinjamBuku(${book.id}); closeDetailModal();" 
                            class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition flex-1">
                            📖 Pinjam Buku
                        </button>` :
                        `<button disabled 
                            class="px-4 py-2 bg-gray-400 cursor-not-allowed text-white rounded-lg flex-1">
                            ❌ Stok Habis
                        </button>`
                    }
                </div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Fungsi pinjam buku via AJAX
async function pinjamBuku(bookId) {
    console.log('Tombol diklik, bookId:', bookId);
    
    try {
        const response = await fetch("{{ url('pinjam') }}/" + bookId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({})
        });

        const result = await response.json();
        console.log('Response:', result);

        if (response.ok) {
            alert('✅ ' + result.message);
            location.reload();
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
        const response = await fetch("{{ url('api/books') }}");
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

    // =====================================
    // HITUNG JUMLAH PINJAMAN YANG MASIH AKTIF
    // =====================================
    const jumlahPinjaman = loans.filter(l =>
        l.status === 'menunggu' ||
        l.status === 'dipinjam' ||
        l.status === 'menunggu_validasi'
    ).length;

    if (filteredBooks.length === 0) {
        grid.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-10">📚 Tidak ada buku. Silakan tambah buku baru.</div>';
        return;
    }

    filteredBooks.forEach(book => {
        const alreadyBorrowed = loans.some(l =>
            l.book_id === book.id &&
            (
                l.status === 'dipinjam' ||
                l.status === 'menunggu' ||
                l.status === 'menunggu_validasi'
            )
        );

        const available =
            book.stok > 0 &&
            !alreadyBorrowed &&
            jumlahPinjaman < 3;
        
        const kategoriNama = book.category ? book.category.nama : '-';
        
        grid.innerHTML += `
            <div class="bg-white p-2 rounded-xl shadow hover:shadow-lg transition flex flex-col">
                <!-- Bagian atas: info + sampul (flex row) -->
                <div class="flex gap-2">
                    <!-- Info Buku di kiri -->
                    <div class="flex-1">
                        <h3 class="font-bold text-lg">${escapeHtml(book.judul)}</h3>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-hashtag"></i> Kode: ${book.kode_buku || '-'}</p>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-user"></i> ${escapeHtml(book.penulis)}</p>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-tag"></i> ${escapeHtml(kategoriNama)}</p>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-building"></i> ${escapeHtml(book.penerbit) || '-'}</p>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-calendar"></i> ${book.tahun || '-'}</p>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-barcode"></i> ISBN: ${book.isbn || '-'}</p>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-map-pin"></i> Rak: ${book.lokasi_rak || '-'}</p>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-file-alt"></i> Halaman: ${book.jumlah_halaman || '-'}</p>
                        <p class="text-sm mt-1"><i class="fas fa-boxes"></i> Stok: <span class="font-semibold">${book.stok}</span></p>
                    </div>

                    <!-- Sampul di kanan dengan klik untuk detail -->
                    <div class="flex-shrink-0 cursor-pointer" onclick="showDetail(${book.id})" title="Klik untuk melihat detail">
                        ${book.sampul ? 
                            `<img src="{{ url('') }}/${book.sampul}" class="w-40 h-56 object-cover rounded-lg border hover:opacity-80 transition">` : 
                            `<div class="w-40 h-56 bg-gray-200 rounded-lg border flex items-center justify-center text-gray-400 text-xs hover:bg-gray-300 transition">Klik untuk detail</div>`
                        }
                    </div>
                </div>

                <!-- Tombol di bawah (full width, terpisah) -->
                <div class="mt-2 flex gap-2">
                    <button onclick="showDetail(${book.id})" 
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1.5 rounded-lg text-sm transition">
                        📋 Detail
                    </button>
                    <button onclick="pinjamBuku(${book.id})" 
                        class="flex-1 ${available ? 'bg-indigo-500 hover:bg-indigo-600' : 'bg-gray-400 cursor-not-allowed'} text-white py-1.5 rounded-lg text-sm transition"
                        ${!available ? 'disabled' : ''}>
                        ${
                            alreadyBorrowed
                            ? '📌 Sedang Dipinjam'
                            : jumlahPinjaman >= 3
                            ? '🚫 Batas Pinjaman Tercapai'
                            : book.stok > 0
                            ? '📖 Ajukan Peminjaman'
                            : '❌ Stok Habis'
                        }
                    </button>
                </div>
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