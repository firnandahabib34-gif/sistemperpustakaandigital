@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Peminjaman Buku</h1>
        <p class="text-gray-500 text-sm">Kelola peminjaman buku oleh anggota</p>
    </div>
    <div class="flex gap-2">
        <button onclick="filterLoans('semua')" class="px-3 py-1 rounded text-sm bg-gray-200 hover:bg-gray-300">Semua</button>
        <button onclick="filterLoans('menunggu')" class="px-3 py-1 rounded text-sm bg-yellow-100 text-yellow-700 hover:bg-yellow-200">Menunggu</button>
        <button onclick="filterLoans('dipinjam')" class="px-3 py-1 rounded text-sm bg-blue-100 text-blue-700 hover:bg-blue-200">Dipinjam</button>
        <button onclick="filterLoans('dikembalikan')" class="px-3 py-1 rounded text-sm bg-green-100 text-green-700 hover:bg-green-200">Dikembalikan</button>
        <button onclick="filterLoans('ditolak')" class="px-3 py-1 rounded text-sm bg-red-100 text-red-700 hover:bg-red-200">Ditolak</button>
    </div>
</div>

<!-- Search -->
<input id="search" type="text" placeholder="Cari NIM anggota atau judul buku..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Tabel Peminjaman -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-600">ID</th>
                    <th class="text-left p-4 font-semibold text-gray-600">NIM</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Nama Anggota</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Judul Buku</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Tgl Pinjam</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Jatuh Tempo</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Status</th>
                    <th class="text-center p-4 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody id="loansTable">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail & Proses -->
<div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h2 id="modalTitle" class="text-lg font-bold">Proses Peminjaman</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">NIM Anggota</label>
                <p id="detailNim" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Anggota</label>
                <p id="detailName" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Judul Buku</label>
                <p id="detailTitle" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tanggal Pengajuan</label>
                <p id="detailDate" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="flex gap-2" id="actionButtons">
                <!-- Tombol aksi akan diisi JS -->
            </div>
        </div>
    </div>
</div>

<script>
// Data peminjaman dari localStorage
let allLoans = JSON.parse(localStorage.getItem('admin_loans')) || [];
let currentFilter = 'semua';
let currentLoanId = null;

// Sinkron dari anggota ke admin
function syncLoansFromAnggota() {
    const anggotaLoans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    // Simpan ke admin loans jika belum ada
    anggotaLoans.forEach(loan => {
        const exists = allLoans.find(l => l.id === loan.id);
        if (!exists) {
            allLoans.push(loan);
        }
    });
    localStorage.setItem('admin_loans', JSON.stringify(allLoans));
}

function saveLoans() {
    localStorage.setItem('admin_loans', JSON.stringify(allLoans));
    // Sinkron balik ke anggota jika ada perubahan status
    const anggotaLoans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    allLoans.forEach(loan => {
        const anggotaLoan = anggotaLoans.find(l => l.id === loan.id);
        if (anggotaLoan && anggotaLoan.status !== loan.status) {
            anggotaLoan.status = loan.status;
        }
    });
    localStorage.setItem('anggota_loans', JSON.stringify(anggotaLoans));
}

function renderLoans() {
    const search = document.getElementById('search').value.toLowerCase();
    const tbody = document.getElementById('loansTable');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    let filtered = allLoans;
    
    // Filter by status
    if (currentFilter !== 'semua') {
        filtered = filtered.filter(l => l.status === currentFilter);
    }
    
    // Filter by search
    if (search) {
        filtered = filtered.filter(l => 
            l.nim?.toLowerCase().includes(search) || 
            l.userName?.toLowerCase().includes(search) || 
            l.title?.toLowerCase().includes(search)
        );
    }

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-gray-500 py-10">
                    📭 Tidak ada data peminjaman.
                </td>
            </tr>
        `;
        return;
    }

    filtered.forEach(loan => {
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
        
        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 text-sm">${loan.id}</td>
                <td class="p-4 font-mono text-sm">${loan.nim || '-'}</td>
                <td class="p-4">${loan.userName || '-'}</td>
                <td class="p-4 font-medium">${loan.title}</td>
                <td class="p-4 text-sm">${new Date(loan.borrowDate).toLocaleDateString('id-ID')}</td>
                <td class="p-4 text-sm">${new Date(loan.dueDate).toLocaleDateString('id-ID')}</td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded-full text-xs ${statusClass}">${statusText}</span>
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                    <button onclick="processLoan(${loan.id})" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-xs">
                        Proses
                    </button>
                </td>
            </table>
        `;
    });
}

function processLoan(id) {
    const loan = allLoans.find(l => l.id === id);
    if (!loan) return;
    
    currentLoanId = id;
    
    document.getElementById('detailNim').innerText = loan.nim || '-';
    document.getElementById('detailName').innerText = loan.userName || '-';
    document.getElementById('detailTitle').innerText = loan.title;
    document.getElementById('detailDate').innerText = new Date(loan.borrowDate).toLocaleString('id-ID');
    
    const actionDiv = document.getElementById('actionButtons');
    
    if (loan.status === 'menunggu') {
        actionDiv.innerHTML = `
            <button onclick="approveLoan(${id})" class="bg-green-500 text-white px-4 py-2 rounded-lg w-full hover:bg-green-600 transition">
                ✅ Setujui Peminjaman
            </button>
            <button onclick="rejectLoan(${id})" class="bg-red-500 text-white px-4 py-2 rounded-lg w-full hover:bg-red-600 transition">
                ❌ Tolak Peminjaman
            </button>
        `;
    } else if (loan.status === 'dipinjam') {
        actionDiv.innerHTML = `
            <button onclick="returnLoan(${id})" class="bg-blue-500 text-white px-4 py-2 rounded-lg w-full hover:bg-blue-600 transition">
                📖 Proses Pengembalian
            </button>
        `;
    } else {
        actionDiv.innerHTML = `
            <button onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg w-full">
                Tutup
            </button>
        `;
    }
    
    document.getElementById('modal').classList.remove('hidden');
}

function approveLoan(id) {
    const loan = allLoans.find(l => l.id === id);
    if (loan) {
        loan.status = 'dipinjam';
        saveLoans();
        renderLoans();
        closeModal();
        
        // Tambah notifikasi ke anggota
        addNotificationToAnggota(loan.nim, `✅ Peminjaman buku "${loan.title}" telah disetujui. Jatuh tempo: ${new Date(loan.dueDate).toLocaleDateString('id-ID')}`);
        
        alert('✅ Peminjaman disetujui!');
    }
}

function rejectLoan(id) {
    const loan = allLoans.find(l => l.id === id);
    if (loan) {
        // Kembalikan stok buku
        const anggotaBooks = JSON.parse(localStorage.getItem('anggota_books')) || [];
        const book = anggotaBooks.find(b => b.id === loan.bookId);
        if (book) {
            book.stock++;
            localStorage.setItem('anggota_books', JSON.stringify(anggotaBooks));
        }
        
        loan.status = 'ditolak';
        saveLoans();
        renderLoans();
        closeModal();
        
        // Tambah notifikasi ke anggota
        addNotificationToAnggota(loan.nim, `❌ Peminjaman buku "${loan.title}" ditolak.`);
        
        alert('❌ Peminjaman ditolak!');
    }
}

function returnLoan(id) {
    const loan = allLoans.find(l => l.id === id);
    if (loan && confirm(`Proses pengembalian buku "${loan.title}"?`)) {
        loan.status = 'dikembalikan';
        
        // Kembalikan stok buku
        const anggotaBooks = JSON.parse(localStorage.getItem('anggota_books')) || [];
        const book = anggotaBooks.find(b => b.id === loan.bookId);
        if (book) {
            book.stock++;
            localStorage.setItem('anggota_books', JSON.stringify(anggotaBooks));
        }
        
        saveLoans();
        renderLoans();
        closeModal();
        
        // Tambah notifikasi ke anggota
        addNotificationToAnggota(loan.nim, `📚 Buku "${loan.title}" telah dikembalikan. Terima kasih!`);
        
        alert('✅ Buku berhasil dikembalikan!');
    }
}

function addNotificationToAnggota(nim, message) {
    const anggotaNotif = JSON.parse(localStorage.getItem('anggota_notifications')) || [];
    anggotaNotif.unshift({
        id: Date.now(),
        message: message,
        created_at: new Date().toLocaleString()
    });
    if (anggotaNotif.length > 10) anggotaNotif.pop();
    localStorage.setItem('anggota_notifications', JSON.stringify(anggotaNotif));
}

function filterLoans(status) {
    currentFilter = status;
    renderLoans();
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}

// Search event
document.getElementById('search').addEventListener('input', renderLoans);

// Sync data dari anggota
syncLoansFromAnggota();
renderLoans();
</script>

@endsection