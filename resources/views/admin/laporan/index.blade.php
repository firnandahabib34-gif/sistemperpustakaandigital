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
            <!-- Data akan diisi JS -->
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="font-bold text-lg mb-4">Buku Terpopuler</h3>
        <div id="popularBooks" class="space-y-3">
            <!-- Data akan diisi JS -->
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
                    <th class="text-left p-4">Tgl Kembali</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-left p-4">Denda</th>
                </tr>
            </thead>
            <tbody id="riwayatTable">
                <!-- Data akan diisi JS -->
            </tbody>
        </table>
    </div>
</div>

<script>
function loadLaporan() {
    const loans = JSON.parse(localStorage.getItem('admin_loans')) || [];
    
    // Statistik
    const total = loans.length;
    const dipinjam = loans.filter(l => l.status === 'dipinjam').length;
    const dikembalikan = loans.filter(l => l.status === 'dikembalikan').length;
    const totalDenda = loans.reduce((sum, l) => sum + (l.denda || 0), 0);
    
    document.getElementById('totalPeminjaman').innerText = total;
    document.getElementById('sedangDipinjam').innerText = dipinjam;
    document.getElementById('sudahKembali').innerText = dikembalikan;
    document.getElementById('totalDenda').innerHTML = `Rp ${totalDenda.toLocaleString('id-ID')}`;
    
    // Status Chart
    const statusHtml = `
        <div class="flex justify-between items-center">
            <span>Menunggu</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-500 rounded-full" style="width: ${(loans.filter(l => l.status === 'menunggu').length / total * 100) || 0}%"></div>
            </div>
            <span class="font-semibold">${loans.filter(l => l.status === 'menunggu').length}</span>
        </div>
        <div class="flex justify-between items-center">
            <span>Dipinjam</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: ${(dipinjam / total * 100) || 0}%"></div>
            </div>
            <span class="font-semibold">${dipinjam}</span>
        </div>
        <div class="flex justify-between items-center">
            <span>Dikembalikan</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" style="width: ${(dikembalikan / total * 100) || 0}%"></div>
            </div>
            <span class="font-semibold">${dikembalikan}</span>
        </div>
        <div class="flex justify-between items-center">
            <span>Ditolak</span>
            <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-red-500 rounded-full" style="width: ${(loans.filter(l => l.status === 'ditolak').length / total * 100) || 0}%"></div>
            </div>
            <span class="font-semibold">${loans.filter(l => l.status === 'ditolak').length}</span>
        </div>
    `;
    document.getElementById('statusChart').innerHTML = statusHtml;
    
    // Buku Terpopuler
    const bookCount = {};
    loans.forEach(loan => {
        bookCount[loan.title] = (bookCount[loan.title] || 0) + 1;
    });
    const popular = Object.entries(bookCount).sort((a,b) => b[1] - a[1]).slice(0,5);
    let popularHtml = '';
    popular.forEach(([title, count]) => {
        popularHtml += `
            <div class="flex justify-between items-center border-b pb-2">
                <span>📖 ${title}</span>
                <span class="font-semibold">${count} kali</span>
            </div>
        `;
    });
    if (popularHtml === '') popularHtml = '<p class="text-gray-500">Belum ada data peminjaman</p>';
    document.getElementById('popularBooks').innerHTML = popularHtml;
    
    // Riwayat Terbaru (10 terakhir)
    const tbody = document.getElementById('riwayatTable');
    const recent = loans.slice(-10).reverse();
    tbody.innerHTML = '';
    recent.forEach(loan => {
        const statusClass = {
            'menunggu': 'bg-yellow-100 text-yellow-700',
            'dipinjam': 'bg-blue-100 text-blue-700',
            'dikembalikan': 'bg-green-100 text-green-700',
            'ditolak': 'bg-red-100 text-red-700'
        }[loan.status] || 'bg-gray-100 text-gray-700';
        
        const statusText = {
            'menunggu': 'Menunggu',
            'dipinjam': 'Dipinjam',
            'dikembalikan': 'Kembali',
            'ditolak': 'Ditolak'
        }[loan.status] || loan.status;
        
        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 font-mono">${loan.nim || '-'}</td>
                <td class="p-4">${loan.userName || '-'}</td>
                <td class="p-4 font-medium">${loan.title}</td>
                <td class="p-4">${new Date(loan.borrowDate).toLocaleDateString('id-ID')}</td>
                <td class="p-4">${new Date(loan.dueDate).toLocaleDateString('id-ID')}</td>
                <td class="p-4"><span class="px-2 py-1 rounded-full text-xs ${statusClass}">${statusText}</span></td>
                <td class="p-4">${loan.denda ? 'Rp ' + loan.denda.toLocaleString('id-ID') : '-'}</td>
            </tr>
        `;
    });
}

loadLaporan();
</script>

@endsection