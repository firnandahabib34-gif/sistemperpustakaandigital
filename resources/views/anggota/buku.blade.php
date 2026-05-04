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
// Ambil data user yang login
const loggedInUser = JSON.parse(localStorage.getItem('logged_in'));

// Inisialisasi data buku (dengan penerbit & tahun)
function initBooks() {
    let adminBooks = localStorage.getItem('admin_books');
    let anggotaBooks = localStorage.getItem('anggota_books');
    
    if (adminBooks && !anggotaBooks) {
        const books = JSON.parse(adminBooks);
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
    
    if (!localStorage.getItem('anggota_books')) {
        const defaultBooks = [
            { id: 1, title: "Laravel 11", author: "Taylor Otwell", category: "Teknologi", stock: 5, year: 2024, penerbit: "O'Reilly Media" },
            { id: 2, title: "Tailwind CSS", author: "Adam Wathan", category: "Teknologi", stock: 3, year: 2023, penerbit: "Tailwind Labs" },
            { id: 3, title: "Pemrograman Web", author: "Sandhika Galih", category: "Teknologi", stock: 4, year: 2024, penerbit: "UNPAS Press" },
            { id: 4, title: "Basis Data", author: "Rosa A.S.", category: "Teknologi", stock: 2, year: 2023, penerbit: "Informatika" }
        ];
        localStorage.setItem('anggota_books', JSON.stringify(defaultBooks));
    }
}

let books = JSON.parse(localStorage.getItem('anggota_books')) || [];
let loans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
let notifications = JSON.parse(localStorage.getItem('anggota_notifications')) || [];

function saveToStorage() {
    localStorage.setItem('anggota_books', JSON.stringify(books));
    localStorage.setItem('anggota_loans', JSON.stringify(loans));
    localStorage.setItem('anggota_notifications', JSON.stringify(notifications));
}

function addNotification(message) {
    notifications.unshift({
        id: Date.now(),
        message: message,
        created_at: new Date().toLocaleString()
    });
    if (notifications.length > 10) notifications.pop();
    saveToStorage();
    updateNotifBadge();
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
        msg += `📌 ${n.message}\n   📅 ${n.created_at}\n\n`;
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

function renderBooks() {
    const searchValue = document.getElementById('search').value.toLowerCase();
    const grid = document.getElementById('booksGrid');
    if (!grid) return;
    
    grid.innerHTML = "";

    const filteredBooks = books.filter(b => 
        b.title.toLowerCase().includes(searchValue) || 
        b.author.toLowerCase().includes(searchValue)
    );

    if (filteredBooks.length === 0) {
        grid.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-10">📚 Tidak ada buku yang ditemukan.</div>';
        return;
    }

    filteredBooks.forEach(book => {
        const alreadyBorrowed = loans.some(l => l.bookId === book.id && l.status === 'dipinjam');
        const available = book.stock > 0 && !alreadyBorrowed;
        
        grid.innerHTML += `
            <div class="bg-white p-4 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="font-bold text-lg">${escapeHtml(book.title)}</h3>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-user"></i> ${escapeHtml(book.author)}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-tag"></i> ${book.category || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-building"></i> ${book.penerbit || '-'}</p>
                <p class="text-sm text-gray-500 mt-1"><i class="fas fa-calendar"></i> ${book.year || '-'}</p>
                <p class="text-sm mt-1"><i class="fas fa-boxes"></i> Stok: <span class="font-semibold">${book.stock}</span></p>
                
                <button onclick="borrowBook(${book.id})" 
                    class="w-full mt-4 ${available ? 'bg-indigo-500 hover:bg-indigo-600' : 'bg-gray-400 cursor-not-allowed'} text-white py-2 rounded-lg transition"
                    ${!available ? 'disabled' : ''}>
                    ${alreadyBorrowed ? '📌 Sedang Dipinjam' : (book.stock > 0 ? '📖 Ajukan Peminjaman' : '❌ Stok Habis')}
                </button>
            </div>
        `;
    });
}

function borrowBook(bookId) {
    const book = books.find(b => b.id === bookId);
    const alreadyBorrowed = loans.some(l => l.bookId === bookId && l.status === 'dipinjam');
    
    if (!book || book.stock <= 0 || alreadyBorrowed) {
        alert('Buku tidak tersedia untuk dipinjam!');
        return;
    }
    
    if (confirm(`Ajukan pinjaman buku "${book.title}"?`)) {
        book.stock--;
        localStorage.setItem('anggota_books', JSON.stringify(books));
        
        // Sinkron ke admin books
        let adminBooks = JSON.parse(localStorage.getItem('admin_books')) || [];
        const adminBook = adminBooks.find(b => b.id === bookId);
        if (adminBook) adminBook.stock = book.stock;
        localStorage.setItem('admin_books', JSON.stringify(adminBooks));
        
        const borrowDate = new Date();
        const dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + 7);
        
        const newLoan = {
            id: Date.now(),
            bookId: book.id,
            title: book.title,
            nim: loggedInUser?.nim || '20230001',
            userName: loggedInUser?.name || 'Anggota',
            borrowDate: borrowDate.toISOString(),
            dueDate: dueDate.toISOString(),
            status: 'menunggu',
            denda: 0
        };
        
        loans.push(newLoan);
        
        // Sinkron ke admin loans
        let adminLoans = JSON.parse(localStorage.getItem('admin_loans')) || [];
        adminLoans.push(newLoan);
        localStorage.setItem('admin_loans', JSON.stringify(adminLoans));
        
        addNotification(`📖 Pengajuan pinjaman buku "${book.title}" berhasil dikirim. Menunggu persetujuan admin.`);
        
        saveToStorage();
        renderBooks();
        alert(`✅ Pengajuan pinjaman "${book.title}" berhasil! Menunggu persetujuan admin.`);
    }
}

// Sinkron status dari admin
function syncFromAdmin() {
    const adminLoans = JSON.parse(localStorage.getItem('admin_loans')) || [];
    let updated = false;
    
    adminLoans.forEach(adminLoan => {
        const myLoan = loans.find(l => l.id === adminLoan.id && l.nim === loggedInUser?.nim);
        if (myLoan && myLoan.status !== adminLoan.status) {
            myLoan.status = adminLoan.status;
            myLoan.denda = adminLoan.denda || 0;
            updated = true;
            
            if (adminLoan.status === 'dipinjam') {
                addNotification(`✅ Peminjaman buku "${myLoan.title}" telah disetujui! Jatuh tempo: ${new Date(myLoan.dueDate).toLocaleDateString('id-ID')}`);
            } else if (adminLoan.status === 'ditolak') {
                const book = books.find(b => b.id === myLoan.bookId);
                if (book) book.stock++;
                addNotification(`❌ Peminjaman buku "${myLoan.title}" ditolak oleh admin.`);
            }
        }
    });
    
    if (updated) {
        saveToStorage();
        renderBooks();
    }
}

// ========== PEMINJAMAN SAYA (MODAL) ==========
function showMyLoans() {
    const modal = document.getElementById('loansModal');
    const loansList = document.getElementById('loansList');
    
    if (!modal || !loansList) return;
    
    const loans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    const myLoans = loans.filter(l => l.nim === loggedInUser?.nim);
    
    if (myLoans.length === 0) {
        loansList.innerHTML = '<p class="text-center text-gray-500 py-4">📭 Belum ada peminjaman</p>';
    } else {
        loansList.innerHTML = myLoans.map(loan => {
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
                    <div class="font-bold">📖 ${loan.title}</div>
                    <div class="text-sm text-gray-500 mt-1">📅 Pinjam: ${new Date(loan.borrowDate).toLocaleDateString('id-ID')}</div>
                    <div class="text-sm text-gray-500">⏰ Jatuh tempo: ${new Date(loan.dueDate).toLocaleDateString('id-ID')}</div>
                    <div class="text-sm mt-2">
                        <span class="px-2 py-1 rounded-full text-xs ${statusClass}">${statusText}</span>
                    </div>
                    ${loan.denda > 0 ? `<div class="text-sm text-red-600 mt-1">💰 Denda: Rp ${loan.denda.toLocaleString('id-ID')}</div>` : ''}
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

// Event listener untuk menangkap klik dari sidebar
window.addEventListener('showMyLoans', function() {
    showMyLoans();
});
// ========== SAMPAI SINI ==========

// Event listener search
document.getElementById('search').addEventListener('input', renderBooks);

// Inisialisasi
initBooks();
books = JSON.parse(localStorage.getItem('anggota_books')) || [];
loans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
notifications = JSON.parse(localStorage.getItem('anggota_notifications')) || [];

updateNotifBadge();
renderBooks();
setInterval(syncFromAdmin, 5000);
</script>

@endsection