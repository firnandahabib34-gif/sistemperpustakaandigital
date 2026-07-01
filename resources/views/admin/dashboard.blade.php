@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold">Dashboard Admin</h1>
    <p class="text-gray-500 text-sm">Selamat datang di panel administrasi perpustakaan</p>
</div>

<!-- Statistik Cards Baris 1 -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Buku</p>
                <p class="text-2xl font-bold" id="totalBooks">0</p>
            </div>
            <i class="fas fa-book text-blue-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Anggota</p>
                <p class="text-2xl font-bold" id="totalAnggota">0</p>
            </div>
            <i class="fas fa-users text-green-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Peminjaman Aktif</p>
                <p class="text-2xl font-bold" id="activeLoans">0</p>
            </div>
            <i class="fas fa-clock text-yellow-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold" id="pendingLoans">0</p>
            </div>
            <i class="fas fa-hourglass-half text-orange-500 text-3xl"></i>
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

<!-- Statistik Peminjaman (Laporan) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Peminjaman</p>
                <p class="text-2xl font-bold" id="totalPeminjaman">0</p>
            </div>
            <i class="fas fa-hand-holding-heart text-indigo-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-teal-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Menunggu Validasi</p>
                <p class="text-2xl font-bold" id="menungguValidasi">0</p>
            </div>
            <i class="fas fa-clock text-teal-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-pink-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Sudah Kembali</p>
                <p class="text-2xl font-bold" id="sudahKembali">0</p>
            </div>
            <i class="fas fa-check-circle text-pink-500 text-3xl"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-gray-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Ditolak</p>
                <p class="text-2xl font-bold" id="ditolak">0</p>
            </div>
            <i class="fas fa-times-circle text-gray-500 text-3xl"></i>
        </div>
    </div>
</div>

<!-- Grafik Peminjaman -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="font-bold text-lg mb-4">📊 Status Peminjaman</h3>
        <div id="statusChart" class="space-y-3">
            <div class="text-center text-gray-500 py-4">Memuat data...</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="font-bold text-lg mb-4">📚 Buku Terpopuler</h3>
        <div id="popularBooks" class="space-y-3">
            <div class="text-center text-gray-500 py-4">Memuat data...</div>
        </div>
    </div>
</div>


<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function loadDashboard() {
try {
    // Data buku & anggota
    const booksRes = await fetch("{{ url('/api/books') }}");
    const books = await booksRes.json();
    document.getElementById('totalBooks').innerText = books.length;

    const anggotaRes = await fetch("{{ url('/api/anggota') }}");
    const anggota = await anggotaRes.json();
    document.getElementById('totalAnggota').innerText = anggota.length;

    // Data peminjaman
    const loansRes = await fetch("{{ url('/api/admin/loans') }}");
    const loans = await loansRes.json();
    
    // ... sisa kode filter di bawahnya sudah betul ...
        
        const activeLoans = loans.filter(l => l.status === 'dipinjam').length;
        const pendingLoans = loans.filter(l => l.status === 'menunggu').length;
        const totalDenda = loans.reduce((sum, l) => sum + (l.fine || 0), 0);
        const totalPeminjaman = loans.length;
        const menungguValidasi = loans.filter(l => l.status === 'menunggu_validasi').length;
        const sudahKembali = loans.filter(l => l.status === 'dikembalikan').length;
        const ditolak = loans.filter(l => l.status === 'ditolak').length;
        
        document.getElementById('activeLoans').innerText = activeLoans;
        document.getElementById('pendingLoans').innerText = pendingLoans;
        document.getElementById('totalDenda').innerHTML = `Rp ${totalDenda.toLocaleString('id-ID')}`;
        document.getElementById('totalPeminjaman').innerText = totalPeminjaman;
        document.getElementById('menungguValidasi').innerText = menungguValidasi;
        document.getElementById('sudahKembali').innerText = sudahKembali;
        document.getElementById('ditolak').innerText = ditolak;
        
        // Status Chart
        const menunggu = loans.filter(l => l.status === 'menunggu').length;
        const dipinjam = activeLoans;
        
        const statusHtml = `
            <div class="flex justify-between items-center">
                <span>⏳ Menunggu</span>
                <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-500 rounded-full" style="width: ${totalPeminjaman > 0 ? (menunggu / totalPeminjaman * 100) : 0}%"></div>
                </div>
                <span class="font-semibold">${menunggu}</span>
            </div>
            <div class="flex justify-between items-center">
                <span>⏳ Menunggu Validasi</span>
                <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 rounded-full" style="width: ${totalPeminjaman > 0 ? (menungguValidasi / totalPeminjaman * 100) : 0}%"></div>
                </div>
                <span class="font-semibold">${menungguValidasi}</span>
            </div>
            <div class="flex justify-between items-center">
                <span>📖 Dipinjam</span>
                <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: ${totalPeminjaman > 0 ? (dipinjam / totalPeminjaman * 100) : 0}%"></div>
                </div>
                <span class="font-semibold">${dipinjam}</span>
            </div>
            <div class="flex justify-between items-center">
                <span>✅ Dikembalikan</span>
                <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: ${totalPeminjaman > 0 ? (sudahKembali / totalPeminjaman * 100) : 0}%"></div>
                </div>
                <span class="font-semibold">${sudahKembali}</span>
            </div>
            <div class="flex justify-between items-center">
                <span>❌ Ditolak</span>
                <div class="flex-1 mx-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500 rounded-full" style="width: ${totalPeminjaman > 0 ? (ditolak / totalPeminjaman * 100) : 0}%"></div>
                </div>
                <span class="font-semibold">${ditolak}</span>
            </div>
        `;
        document.getElementById('statusChart').innerHTML = statusHtml;
        
        // Buku Terpopuler
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
        
    } catch (error) {
        console.error('Gagal load dashboard:', error);
    }
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

loadDashboard();
setInterval(loadDashboard, 30000);
</script>

@endsection