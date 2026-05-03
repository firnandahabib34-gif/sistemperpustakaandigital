@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold">Peminjaman</h1>
    <p class="text-gray-500 text-sm">Daftar peminjaman buku Anda</p>
</div>

<!-- Filter Status -->
<div class="flex flex-wrap gap-2 mb-6">
    <button onclick="filterLoans('semua')" id="filterSemua" class="px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white transition">
        Semua
    </button>
    <button onclick="filterLoans('menunggu')" id="filterMenunggu" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
        ⏳ Menunggu
    </button>
    <button onclick="filterLoans('dipinjam')" id="filterDipinjam" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
        📖 Dipinjam
    </button>
    <button onclick="filterLoans('dikembalikan')" id="filterKembali" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
        ✅ Dikembalikan
    </button>
    <button onclick="filterLoans('ditolak')" id="filterDitolak" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
        ❌ Ditolak
    </button>
</div>

<!-- Grid Peminjaman -->
<div id="loansGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

<script>
const loggedInUser = JSON.parse(localStorage.getItem('logged_in'));
let currentFilter = 'semua';
let allLoans = [];

function loadLoans() {
    const loans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    allLoans = loans.filter(l => l.nim === loggedInUser?.nim);
    renderLoans();
}

function renderLoans() {
    const grid = document.getElementById('loansGrid');
    if (!grid) return;
    
    let filtered = allLoans;
    
    if (currentFilter !== 'semua') {
        filtered = allLoans.filter(l => l.status === currentFilter);
    }
    
    if (filtered.length === 0) {
        let message = '';
        if (currentFilter === 'semua') message = '📭 Belum ada peminjaman';
        else if (currentFilter === 'menunggu') message = '⏳ Tidak ada peminjaman yang menunggu';
        else if (currentFilter === 'dipinjam') message = '📖 Tidak ada buku yang sedang dipinjam';
        else if (currentFilter === 'dikembalikan') message = '✅ Belum ada riwayat pengembalian';
        else if (currentFilter === 'ditolak') message = '❌ Tidak ada peminjaman yang ditolak';
        else message = '📭 Tidak ada data';
        
        grid.innerHTML = `
            <div class="col-span-3 text-center text-gray-500 py-10">
                ${message}
            </div>
        `;
        return;
    }
    
    grid.innerHTML = filtered.map(loan => {
        const statusConfig = {
            'menunggu': { bg: 'bg-yellow-100', text: 'text-yellow-700', icon: '⏳', label: 'Menunggu Persetujuan', border: 'border-yellow-300' },
            'dipinjam': { bg: 'bg-blue-100', text: 'text-blue-700', icon: '📖', label: 'Sedang Dipinjam', border: 'border-blue-300' },
            'dikembalikan': { bg: 'bg-green-100', text: 'text-green-700', icon: '✅', label: 'Sudah Dikembalikan', border: 'border-green-300' },
            'ditolak': { bg: 'bg-red-100', text: 'text-red-700', icon: '❌', label: 'Ditolak', border: 'border-red-300' }
        };
        
        const config = statusConfig[loan.status] || statusConfig['menunggu'];
        
        // Hitung keterlambatan jika status dipinjam
        let lateInfo = '';
        if (loan.status === 'dipinjam') {
            const dueDate = new Date(loan.dueDate);
            const today = new Date();
            if (today > dueDate) {
                const lateDays = Math.ceil((today - dueDate) / (1000 * 60 * 60 * 24));
                lateInfo = `<div class="mt-2 p-2 bg-red-50 rounded-lg">
                                <span class="text-red-600 text-sm">⚠️ Terlambat ${lateDays} hari</span>
                            </div>`;
            }
        }
        
        // Hitung denda
        let dendaInfo = '';
        if (loan.denda > 0) {
            dendaInfo = `<div class="mt-2 p-2 bg-red-50 rounded-lg">
                            <span class="text-red-600 text-sm">💰 Denda: Rp ${loan.denda.toLocaleString('id-ID')}</span>
                         </div>`;
        }
        
        return `
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition border-l-4 ${config.border} overflow-hidden">
                <div class="p-5">
                    <!-- Header Card -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-800">${escapeHtml(loan.title)}</h3>
                            <p class="text-sm text-gray-500 mt-1">ID Peminjaman: #${loan.id}</p>
                        </div>
                        <div class="px-3 py-1 rounded-full text-xs font-semibold ${config.bg} ${config.text}">
                            ${config.icon} ${config.label}
                        </div>
                    </div>
                    
                    <!-- Detail Peminjaman -->
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-calendar-alt w-4 text-gray-400"></i>
                            <span>Pinjam: ${new Date(loan.borrowDate).toLocaleDateString('id-ID')}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-hourglass-end w-4 text-gray-400"></i>
                            <span>Jatuh Tempo: ${new Date(loan.dueDate).toLocaleDateString('id-ID')}</span>
                        </div>
                        ${loan.returnDate ? `
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-undo-alt w-4 text-gray-400"></i>
                            <span>Dikembalikan: ${new Date(loan.returnDate).toLocaleDateString('id-ID')}</span>
                        </div>
                        ` : ''}
                    </div>
                    
                    <!-- Info Terlambat & Denda -->
                    ${lateInfo}
                    ${dendaInfo}
                    
                    <!-- Tombol Aksi (hanya untuk yang sedang dipinjam) -->
                    ${loan.status === 'dipinjam' ? `
                    <div class="mt-4 pt-3 border-t">
                        <button onclick="alert('Silakan kembalikan buku ke petugas perpustakaan')" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-sm font-medium transition">
                            📖 Kembalikan Buku
                        </button>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
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

function filterLoans(status) {
    currentFilter = status;
    
    // Update style tombol filter
    const filters = ['semua', 'menunggu', 'dipinjam', 'dikembalikan', 'ditolak'];
    filters.forEach(f => {
        const btn = document.getElementById(`filter${f.charAt(0).toUpperCase() + f.slice(1)}`);
        if (btn) {
            if (f === status) {
                btn.className = 'px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white transition';
            } else {
                btn.className = 'px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition';
            }
        }
    });
    
    renderLoans();
}

// Sinkronasi otomatis setiap 3 detik
function syncLoans() {
    const adminLoans = JSON.parse(localStorage.getItem('admin_loans')) || [];
    const myLoans = allLoans.map(loan => {
        const adminLoan = adminLoans.find(a => a.id === loan.id);
        if (adminLoan && adminLoan.status !== loan.status) {
            loan.status = adminLoan.status;
            loan.denda = adminLoan.denda || 0;
            if (adminLoan.status === 'dikembalikan') {
                loan.returnDate = adminLoan.returnDate || new Date().toISOString();
            }
        }
        return loan;
    });
    
    // Simpan kembali ke localStorage
    const allAnggotaLoans = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    myLoans.forEach(updatedLoan => {
        const index = allAnggotaLoans.findIndex(l => l.id === updatedLoan.id);
        if (index !== -1) {
            allAnggotaLoans[index] = updatedLoan;
        }
    });
    localStorage.setItem('anggota_loans', JSON.stringify(allAnggotaLoans));
    
    // Reload data
    loadLoans();
}

// Load data awal
loadLoans();

// Sinkron setiap 3 detik
setInterval(syncLoans, 3000);
</script>

@endsection