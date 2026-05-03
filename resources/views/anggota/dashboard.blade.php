@extends('layouts.app')

@section('title', 'Dashboard Anggota')

@section('content')

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold">Dashboard Anggota</h1>
        <p class="text-gray-500 text-sm">Selamat Datang Di Perpustakaan Digital</p>
    </div>
    <button onclick="showNotifications()" class="bg-white px-4 py-2 rounded-lg shadow flex items-center gap-2 hover:bg-gray-50 transition relative">
        <i class="fas fa-bell"></i> Notifikasi
        <span id="notifBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5 min-w-[20px] text-center hidden">0</span>
    </button>
</div>


<!-- Statistik Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Buku Dipinjam</p>
                <p class="text-2xl font-bold" id="totalDipinjam">0</p>
            </div>
            <i class="fas fa-book-open text-blue-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold" id="totalMenunggu">0</p>
            </div>
            <i class="fas fa-hourglass-half text-yellow-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Denda</p>
                <p class="text-2xl font-bold" id="totalDenda">Rp 0</p>
            </div>
            <i class="fas fa-money-bill text-red-500 text-3xl"></i>
        </div>
    </div>
</div>

<!-- Menu Cepat -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="/dashboard-anggota/buku" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-book text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">Koleksi Buku</h3>
                <p class="text-gray-500 text-sm">Cari dan pinjam buku</p>
            </div>
        </div>
    </a>
    
    <a href="#" onclick="showMyLoans()" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-hand-holding-heart text-green-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">Peminjaman</h3>
                <p class="text-gray-500 text-sm">Lihat status peminjaman</p>
            </div>
        </div>
    </a>
</div>

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
const loggedInUser = JSON.parse(localStorage.getItem('logged_in'));

function updateStats() {
    const loans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    const myLoans = loans.filter(l => l.nim === loggedInUser?.nim);
    
    const dipinjam = myLoans.filter(l => l.status === 'dipinjam').length;
    const menunggu = myLoans.filter(l => l.status === 'menunggu').length;
    const totalDenda = myLoans.reduce((sum, l) => sum + (l.denda || 0), 0);
    
    const totalDipinjamEl = document.getElementById('totalDipinjam');
    const totalMenungguEl = document.getElementById('totalMenunggu');
    const totalDendaEl = document.getElementById('totalDenda');
    
    if (totalDipinjamEl) totalDipinjamEl.innerText = dipinjam;
    if (totalMenungguEl) totalMenungguEl.innerText = menunggu;
    if (totalDendaEl) totalDendaEl.innerHTML = `Rp ${totalDenda.toLocaleString('id-ID')}`;
}

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

// Event listener untuk showMyLoans dari sidebar
window.showMyLoans = showMyLoans;

// Update statistik setiap kali halaman dimuat
updateStats();

// Sinkron setiap 5 detik
setInterval(updateStats, 5000);
</script>

@endsection