@extends('layouts.app')

@section('title', 'Pengembalian Buku')

@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Pengembalian Buku</h1>
        <p class="text-gray-500 text-sm">Proses pengembalian buku yang sudah dikonfirmasi anggota</p>
    </div>
    <div class="flex gap-2">
        <button onclick="filterPengembalian('semua')" id="filterSemua" class="px-3 py-1 rounded text-sm bg-gray-200 hover:bg-gray-300">Semua</button>
        <button onclick="filterPengembalian('menunggu_validasi')" id="filterMenunggu" class="px-3 py-1 rounded text-sm bg-purple-100 text-purple-700 hover:bg-purple-200">Menunggu Validasi</button>
        <button onclick="filterPengembalian('dikembalikan')" id="filterKembali" class="px-3 py-1 rounded text-sm bg-green-100 text-green-700 hover:bg-green-200">Sudah Kembali</button>
    </div>
</div>

<!-- Pencarian -->
<input id="cari" type="text" placeholder="Cari NIM anggota atau judul buku..." 
    class="w-full mb-4 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">

<!-- Tabel Pengembalian -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-600">ID</th>
                    <th class="text-left p-4 font-semibold text-gray-600">NIM</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Nama Anggota</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Judul Buku</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Tanggal Pinjam</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Jatuh Tempo</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Terlambat</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Denda</th>
                    <th class="text-left p-4 font-semibold text-gray-600">Status</th>
                    <th class="text-center p-4 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPengembalian">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Proses Pengembalian -->
<div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-bold">Proses Pengembalian</h2>
            <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">NIM Anggota</label>
                <p id="detailNim" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Anggota</label>
                <p id="detailNama" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Judul Buku</label>
                <p id="detailJudul" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tanggal Pinjam</label>
                <p id="detailTglPinjam" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Jatuh Tempo</label>
                <p id="detailJatuhTempo" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Terlambat</label>
                <p id="detailTerlambat" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Denda</label>
                <p id="detailDenda" class="text-gray-700 bg-gray-50 p-2 rounded"></p>
            </div>
            
            <div class="flex gap-2">
                <button onclick="prosesKembalikan()" class="bg-green-500 text-white px-4 py-2 rounded-lg w-full hover:bg-green-600 transition">
                    ✅ Konfirmasi Pengembalian
                </button>
                <button onclick="tutupModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg w-full hover:bg-gray-500 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Data dari database
let semuaPeminjaman = [];
let filterSaatIni = 'semua';
let peminjamanIdSaatIni = null;

// Load data dari database
async function loadLoans() {
    try {
        const response = await fetch('/api/admin/loans');
        semuaPeminjaman = await response.json();
        tampilkanPengembalian();
    } catch (error) {
        console.error('Gagal load data:', error);
    }
}

function hitungTerlambat(tanggalJatuhTempo) {
    const hariIni = new Date();
    const jatuhTempo = new Date(tanggalJatuhTempo);
    if (hariIni > jatuhTempo) {
        const selisihHari = Math.ceil((hariIni - jatuhTempo) / (1000 * 60 * 60 * 24));
        return selisihHari;
    }
    return 0;
}

function hitungDenda(terlambat) {
    return terlambat * 2000;
}

function tampilkanPengembalian() {
    const cari = document.getElementById('cari').value.toLowerCase();
    const tbody = document.getElementById('tabelPengembalian');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    let filtered = semuaPeminjaman.filter(p => p.status === 'menunggu_validasi' || p.status === 'dikembalikan');
    
    if (filterSaatIni === 'menunggu_validasi') {
        filtered = filtered.filter(p => p.status === 'menunggu_validasi');
    } else if (filterSaatIni === 'dikembalikan') {
        filtered = filtered.filter(p => p.status === 'dikembalikan');
    }
    
    if (cari) {
        filtered = filtered.filter(p => 
            (p.user?.nim && p.user.nim.toLowerCase().includes(cari)) || 
            (p.user?.name && p.user.name.toLowerCase().includes(cari)) || 
            (p.book?.judul && p.book.judul.toLowerCase().includes(cari))
        );
    }

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="text-center text-gray-500 py-10">📭 Tidak ada data pengembalian.</td></tr>`;
        return;
    }

filtered.forEach(peminjaman => {
    const user = peminjaman.user || {};
    const book = peminjaman.book || {};
    
    // 🔥 PERBAIKAN DI SINI
    let terlambat, denda;
    if (peminjaman.status === 'dikembalikan') {
        // Ambil dari database jika sudah dikembalikan
        denda = peminjaman.fine || 0;
        terlambat = denda > 0 ? Math.ceil(denda / 2000) : 0;
    } else {
        // Hitung otomatis jika masih menunggu validasi
        terlambat = hitungTerlambat(peminjaman.due_date);
        denda = hitungDenda(terlambat);
    }
    
    let statusClass = '';
    let statusText = '';
    if (peminjaman.status === 'menunggu_validasi') {
        statusClass = 'bg-purple-100 text-purple-700';
        statusText = 'Menunggu Validasi';
    } else {
        statusClass = 'bg-green-100 text-green-700';
        statusText = 'Dikembalikan';
    }
    
    tbody.innerHTML += `
        <tr class="border-b hover:bg-gray-50">
            <td class="p-4 text-sm">${peminjaman.id}</td>
            <td class="p-4 font-mono text-sm">${user.nim || '-'}</td>
            <td class="p-4">${user.name || '-'}</td>
            <td class="p-4 font-medium">${book.judul || '-'}</td>
            <td class="p-4 text-sm">${new Date(peminjaman.borrow_date).toLocaleDateString('id-ID')}</td>
            <td class="p-4 text-sm">${new Date(peminjaman.due_date).toLocaleDateString('id-ID')}</td>
            <td class="p-4 text-sm ${terlambat > 0 ? 'text-red-600 font-semibold' : 'text-gray-500'}">
                ${terlambat > 0 ? terlambat + ' hari' : '-'}
            </td>
            <td class="p-4 text-sm font-semibold ${denda > 0 ? 'text-red-600' : 'text-gray-500'}">
                ${denda > 0 ? 'Rp ' + denda.toLocaleString('id-ID') : '-'}
            </td>
            <td class="p-4">
                <span class="px-2 py-1 rounded-full text-xs ${statusClass}">${statusText}</span>
            </td>
            <td class="p-4 text-center whitespace-nowrap">
                ${peminjaman.status === 'menunggu_validasi' ? 
                    `<button onclick="bukaModalKembalikan(${peminjaman.id})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                        ✅ Validasi Kembalikan
                    </button>` : 
                    `<span class="text-gray-400 text-xs">Sudah Kembali</span>`
                }
            </td>
        </tr>
    `;
});
}

function bukaModalKembalikan(id) {
    const peminjaman = semuaPeminjaman.find(p => p.id == id);
    if (!peminjaman) return;
    
    peminjamanIdSaatIni = id;
    const user = peminjaman.user || {};
    const book = peminjaman.book || {};
    const terlambat = hitungTerlambat(peminjaman.due_date);
    const denda = hitungDenda(terlambat);
    
    document.getElementById('detailNim').innerText = user.nim || '-';
    document.getElementById('detailNama').innerText = user.name || '-';
    document.getElementById('detailJudul').innerText = book.judul || '-';
    document.getElementById('detailTglPinjam').innerText = new Date(peminjaman.borrow_date).toLocaleDateString('id-ID');
    document.getElementById('detailJatuhTempo').innerText = new Date(peminjaman.due_date).toLocaleDateString('id-ID');
    document.getElementById('detailTerlambat').innerHTML = terlambat > 0 ? `<span class="text-red-600 font-semibold">${terlambat} hari</span>` : 'Tidak terlambat';
    document.getElementById('detailDenda').innerHTML = denda > 0 ? `<span class="text-red-600 font-semibold">Rp ${denda.toLocaleString('id-ID')}</span>` : 'Tidak ada denda';
    
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

async function prosesKembalikan() {
    const id = peminjamanIdSaatIni;
    
    if (confirm('Yakin anggota sudah mengembalikan buku secara fisik?')) {
        try {
            const response = await fetch(`/admin/pengembalian/${id}/validate`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const result = await response.json();
            
            if (response.ok) {
                alert('✅ ' + result.message);
                tutupModal();
                loadLoans(); // Refresh data
            } else {
                alert('❌ ' + (result.message || 'Gagal memproses pengembalian'));
            }
        } catch (error) {
            alert('❌ Terjadi kesalahan: ' + error.message);
        }
    }
}

function filterPengembalian(status) {
    filterSaatIni = status;
    
    const filters = ['semua', 'menunggu_validasi', 'dikembalikan'];
    filters.forEach(f => {
        const btn = document.getElementById(`filter${f.charAt(0).toUpperCase() + f.slice(1)}`);
        if (btn) {
            if (f === status) {
                btn.className = 'px-3 py-1 rounded text-sm bg-indigo-600 text-white';
            } else {
                btn.className = 'px-3 py-1 rounded text-sm bg-gray-200 hover:bg-gray-300';
            }
        }
    });
    
    tampilkanPengembalian();
}

function tutupModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Event pencarian
document.getElementById('cari').addEventListener('input', tampilkanPengembalian);

// Load data awal
loadLoans();
</script>

@endsection