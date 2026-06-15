@extends('layouts.app')

@section('title', 'Laporan Sirkulasi')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold">Laporan Sirkulasi Buku</h1>
    <p class="text-gray-500 text-sm">Statistik peminjaman dan pengembalian buku</p>
</div>

<!-- Statistik Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Peminjaman</p>
                <p class="text-2xl font-bold" id="totalPeminjaman">0</p>
            </div>
            <i class="fas fa-hand-holding-heart text-blue-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Sedang Dipinjam</p>
                <p class="text-2xl font-bold" id="sedangDipinjam">0</p>
            </div>
            <i class="fas fa-book-open text-green-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Sudah Kembali</p>
                <p class="text-2xl font-bold" id="sudahKembali">0</p>
            </div>
            <i class="fas fa-check-circle text-yellow-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Denda Terkumpul</p>
                <p class="text-2xl font-bold" id="totalDenda">Rp 0</p>
            </div>
            <i class="fas fa-money-bill text-red-500 text-3xl"></i>
        </div>
    </div>
</div>

<!-- Grafik Sederhana -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="font-bold text-lg mb-4">Status Peminjaman</h3>
        <div id="statusChart" class="space-y-3">
            <div class="text-center text-gray-500 py-4">Memuat data...</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="font-bold text-lg mb-4">Buku Terpopuler</h3>
        <div id="popularBooks" class="space-y-3">
            <div class="text-center text-gray-500 py-4">Memuat data...</div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat Peminjaman -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-bold text-lg">Riwayat Peminjaman Terbaru</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4">NIM</th>
                    <th class="text-left p-4">Nama Anggota</th>
                    <th class="text-left p-4">Judul Buku</th>
                    <th class="text-left p-4">Tgl Pinjam</th>
                    <th class="text-left p-4">Jatuh Tempo</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-left p-4">Denda</th>
                </tr>
            </thead>
            <tbody id="riwayatTable">
                <tr><td colspan="7" class="text-center py-10 text-gray-500">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

let allLoans = [];

// Load data dari database
async function loadLaporan() {
    try {
        const response = await fetch('/api/admin/loans');
        allLoans = await response.json();
        renderLaporan();
    } catch (error) {
        console.error('Gagal load data:', error);
        document.getElementById('riwayatTable').innerHTML = '<tr><td colspan="7" class="text-center py-10 text-red-500">Gagal memuat data</td></tr>';
    }
}

function renderLaporan() {
    const loans = allLoans;
    
    // Statistik
    const total = loans.length;
    const dipinjam = loans.filter(l => l.status === 'dipinjam').length;
    const dikembalikan = loans.filter(l => l.status === 'dikembalikan').length;
    const totalDenda = loans.reduce((sum, l) => sum + (l.fine || 0), 0);
    
    document.getElementById('totalPeminjaman').innerText = total;
    document.getElementById('sedangDipinjam').innerText = dipinjam;
    document.getElementById('sudahKembali').innerText = dikembalikan;
    document.getElementById('totalDenda').innerHTML = `Rp ${totalDenda.toLocaleString('id-ID')}`;
    
    // Status Chart (dengan persentase)
    const menunggu = loans.filter(l => l.status === 'menunggu').length;
    const menungguValidasi = loans.filter(l => l.status === 'menunggu_validasi').length;
    const ditolak = loans.filter(l => l.status === 'ditolak').length;
    
    const statusHtml = `
        <div class="flex justify-between items-center">
            <span>⏳ Menunggu</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-500 rounded-full" style="width: ${total > 0 ? (menunggu / total * 100) : 0}%"></div>
            </div>
            <span class="font-semibold">${menunggu}</span>
        </div>
        <div class="flex justify-between items-center">
            <span>⏳ Menunggu Validasi</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-purple-500 rounded-full" style="width: ${total > 0 ? (menungguValidasi / total * 100) : 0}%"></div>
            </div>
            <span class="font-semibold">${menungguValidasi}</span>
        </div>
        <div class="flex justify-between items-center">
            <span>📖 Dipinjam</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: ${total > 0 ? (dipinjam / total * 100) : 0}%"></div>
            </div>
            <span class="font-semibold">${dipinjam}</span>
        </div>
        <div class="flex justify-between items-center">
            <span>✅ Dikembalikan</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" style="width: ${total > 0 ? (dikembalikan / total * 100) : 0}%"></div>
            </div>
            <span class="font-semibold">${dikembalikan}</span>
        </div>
        <div class="flex justify-between items-center">
            <span>❌ Ditolak</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-red-500 rounded-full" style="width: ${total > 0 ? (ditolak / total * 100) : 0}%"></div>
            </div>
            <span class="font-semibold">${ditolak}</span>
        </div>
    `;
    document.getElementById('statusChart').innerHTML = statusHtml;
    
    // Buku Terpopuler (berdasarkan jumlah peminjaman)
    const bookCount = {};
    loans.forEach(loan => {
        const bookTitle = loan.book?.judul || 'Tidak diketahui';
        bookCount[bookTitle] = (bookCount[bookTitle] || 0) + 1;
    });
    const popular = Object.entries(bookCount).sort((a,b) => b[1] - a[1]).slice(0,5);
    let popularHtml = '';
    if (popular.length === 0) {
        popularHtml = '<p class="text-gray-500 text-center">Belum ada data peminjaman</p>';
    } else {
        popular.forEach(([title, count]) => {
            popularHtml += `
                <div class="flex justify-between items-center border-b pb-2">
                    <span>📖 ${escapeHtml(title)}</span>
                    <span class="font-semibold">${count} kali</span>
                </div>
            `;
        });
    }
    document.getElementById('popularBooks').innerHTML = popularHtml;
    
    // Riwayat Terbaru (10 terakhir)
    const tbody = document.getElementById('riwayatTable');
    const recent = [...loans].reverse().slice(0, 10);
    
    if (recent.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-10 text-gray-500">Belum ada riwayat peminjaman</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    recent.forEach(loan => {
        const user = loan.user || {};
        const book = loan.book || {};
        
        let statusClass = '';
        let statusText = '';
        switch (loan.status) {
            case 'menunggu':
                statusClass = 'bg-yellow-100 text-yellow-700';
                statusText = '⏳ Menunggu';
                break;
            case 'dipinjam':
                statusClass = 'bg-blue-100 text-blue-700';
                statusText = '📖 Dipinjam';
                break;
            case 'menunggu_validasi':
                statusClass = 'bg-purple-100 text-purple-700';
                statusText = '⏳ Menunggu Validasi';
                break;
            case 'dikembalikan':
                statusClass = 'bg-green-100 text-green-700';
                statusText = '✅ Dikembalikan';
                break;
            case 'ditolak':
                statusClass = 'bg-red-100 text-red-700';
                statusText = '❌ Ditolak';
                break;
            default:
                statusClass = 'bg-gray-100 text-gray-700';
                statusText = loan.status;
        }
        
        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 font-mono">${escapeHtml(user.nim || '-')}</td>
                <td class="p-4">${escapeHtml(user.name || '-')}</td>
                <td class="p-4 font-medium">${escapeHtml(book.judul || '-')}</td>
                <td class="p-4">${new Date(loan.borrow_date).toLocaleDateString('id-ID')}</td>
                <td class="p-4">${new Date(loan.due_date).toLocaleDateString('id-ID')}</td>
                <td class="p-4"><span class="px-2 py-1 rounded-full text-xs ${statusClass}">${statusText}</span></td>
                <td class="p-4">${loan.fine ? 'Rp ' + loan.fine.toLocaleString('id-ID') : '-'}</td>
            </tr>
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

// Load data awal
loadLaporan();

// Refresh setiap 30 detik
setInterval(loadLaporan, 30000);
</script>

@endsection