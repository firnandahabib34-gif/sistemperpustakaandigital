@extends('layouts.app')

@section('title', 'Pengembalian Buku')

@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Pengembalian Buku</h1>
        <p class="text-gray-500 text-sm">Proses pengembalian buku yang sedang dipinjam</p>
    </div>
    <div class="flex gap-2">
        <button onclick="filterPengembalian('semua')" class="px-3 py-1 rounded text-sm bg-gray-200 hover:bg-gray-300">Semua</button>
        <button onclick="filterPengembalian('dipinjam')" class="px-3 py-1 rounded text-sm bg-blue-100 text-blue-700 hover:bg-blue-200">Sedang Dipinjam</button>
        <button onclick="filterPengembalian('terlambat')" class="px-3 py-1 rounded text-sm bg-red-100 text-red-700 hover:bg-red-200">Terlambat</button>
        <button onclick="filterPengembalian('dikembalikan')" class="px-3 py-1 rounded text-sm bg-green-100 text-green-700 hover:bg-green-200">Sudah Kembali</button>
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
                    <th class="text-left p-4 font-semibold text-gray-600">Tgl Pinjam</th>
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
// Data peminjaman
let semuaPeminjaman = JSON.parse(localStorage.getItem('admin_loans')) || [];
let filterSaatIni = 'semua';
let peminjamanIdSaatIni = null;

// Sinkron dari anggota ke admin
function sinkronDariAnggota() {
    const peminjamanAnggota = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    peminjamanAnggota.forEach(pinjam => {
        const exists = semuaPeminjaman.find(l => l.id === pinjam.id);
        if (!exists) {
            semuaPeminjaman.push(pinjam);
        }
    });
    localStorage.setItem('admin_loans', JSON.stringify(semuaPeminjaman));
}

function simpanPeminjaman() {
    localStorage.setItem('admin_loans', JSON.stringify(semuaPeminjaman));
    // Sinkron balik ke anggota
    const peminjamanAnggota = JSON.parse(localStorage.getItem('anggota_loans')) || [];
    semuaPeminjaman.forEach(pinjam => {
        const anggotaPinjam = peminjamanAnggota.find(l => l.id === pinjam.id);
        if (anggotaPinjam && anggotaPinjam.status !== pinjam.status) {
            anggotaPinjam.status = pinjam.status;
            anggotaPinjam.denda = pinjam.denda || 0;
        }
    });
    localStorage.setItem('anggota_loans', JSON.stringify(peminjamanAnggota));
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
    // Denda Rp 2000 per hari
    return terlambat * 2000;
}

function tampilkanPengembalian() {
    const cari = document.getElementById('cari').value.toLowerCase();
    const tbody = document.getElementById('tabelPengembalian');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    let filtered = semuaPeminjaman.filter(p => p.status === 'dipinjam' || p.status === 'dikembalikan');
    
    // Filter by status
    if (filterSaatIni === 'dipinjam') {
        filtered = filtered.filter(p => p.status === 'dipinjam');
    } else if (filterSaatIni === 'terlambat') {
        filtered = filtered.filter(p => {
            if (p.status !== 'dipinjam') return false;
            const terlambat = hitungTerlambat(p.dueDate);
            return terlambat > 0;
        });
    } else if (filterSaatIni === 'dikembalikan') {
        filtered = filtered.filter(p => p.status === 'dikembalikan');
    }
    
    // Filter by search
    if (cari) {
        filtered = filtered.filter(p => 
            p.nim?.toLowerCase().includes(cari) || 
            p.userName?.toLowerCase().includes(cari) || 
            p.title?.toLowerCase().includes(cari)
        );
    }

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-gray-500 py-10">
                    📭 Tidak ada data peminjaman.
                </td>
            </tr>
        `;
        return;
    }

    filtered.forEach(peminjaman => {
        const terlambat = peminjaman.status === 'dipinjam' ? hitungTerlambat(peminjaman.dueDate) : 0;
        const denda = peminjaman.status === 'dipinjam' ? hitungDenda(terlambat) : (peminjaman.denda || 0);
        
        // Update denda di peminjaman
        if (peminjaman.status === 'dipinjam') {
            peminjaman.denda = denda;
        }
        
        const statusClass = peminjaman.status === 'dipinjam' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700';
        const statusText = peminjaman.status === 'dipinjam' ? 'Dipinjam' : 'Dikembalikan';
        
        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 text-sm">${peminjaman.id}</td>
                <td class="p-4 font-mono text-sm">${peminjaman.nim || '-'}</td>
                <td class="p-4">${peminjaman.userName || '-'}</td>
                <td class="p-4 font-medium">${peminjaman.title}</td>
                <td class="p-4 text-sm">${new Date(peminjaman.borrowDate).toLocaleDateString('id-ID')}</td>
                <td class="p-4 text-sm">${new Date(peminjaman.dueDate).toLocaleDateString('id-ID')}</td>
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
                    ${peminjaman.status === 'dipinjam' ? 
                        `<button onclick="bukaModalKembalikan(${peminjaman.id})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                            📖 Kembalikan
                        </button>` : 
                        `<span class="text-gray-400 text-xs">Sudah Kembali</span>`
                    }
                </td>
            </table>
        `;
    });
}

function bukaModalKembalikan(id) {
    const peminjaman = semuaPeminjaman.find(p => p.id === id);
    if (!peminjaman) return;
    
    peminjamanIdSaatIni = id;
    const terlambat = hitungTerlambat(peminjaman.dueDate);
    const denda = hitungDenda(terlambat);
    
    document.getElementById('detailNim').innerText = peminjaman.nim || '-';
    document.getElementById('detailNama').innerText = peminjaman.userName || '-';
    document.getElementById('detailJudul').innerText = peminjaman.title;
    document.getElementById('detailTglPinjam').innerText = new Date(peminjaman.borrowDate).toLocaleDateString('id-ID');
    document.getElementById('detailJatuhTempo').innerText = new Date(peminjaman.dueDate).toLocaleDateString('id-ID');
    document.getElementById('detailTerlambat').innerHTML = terlambat > 0 ? `<span class="text-red-600 font-semibold">${terlambat} hari</span>` : 'Tidak terlambat';
    document.getElementById('detailDenda').innerHTML = denda > 0 ? `<span class="text-red-600 font-semibold">Rp ${denda.toLocaleString('id-ID')}</span>` : 'Tidak ada denda';
    
    document.getElementById('modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function prosesKembalikan() {
    const peminjaman = semuaPeminjaman.find(p => p.id === peminjamanIdSaatIni);
    if (!peminjaman) return;
    
    const terlambat = hitungTerlambat(peminjaman.dueDate);
    const denda = hitungDenda(terlambat);
    
    let pesan = `📖 Konfirmasi pengembalian buku "${peminjaman.title}"`;
    if (denda > 0) {
        pesan += ` dengan denda Rp ${denda.toLocaleString('id-ID')}`;
    }
    pesan += `?`;
    
    if (confirm(pesan)) {
        // Update status peminjaman
        peminjaman.status = 'dikembalikan';
        peminjaman.denda = denda;
        peminjaman.tanggalKembali = new Date().toISOString();
        
        // Kembalikan stok buku
        const bukuAnggota = JSON.parse(localStorage.getItem('anggota_books')) || [];
        const buku = bukuAnggota.find(b => b.id === peminjaman.bookId);
        if (buku) {
            buku.stock++;
            localStorage.setItem('anggota_books', JSON.stringify(bukuAnggota));
            
            // Sinkron ke admin books
            const bukuAdmin = JSON.parse(localStorage.getItem('admin_books')) || [];
            const bukuAdminTarget = bukuAdmin.find(b => b.id === peminjaman.bookId);
            if (bukuAdminTarget) {
                bukuAdminTarget.stock = buku.stock;
                localStorage.setItem('admin_books', JSON.stringify(bukuAdmin));
            }
        }
        
        simpanPeminjaman();
        
        // Tambah notifikasi ke anggota
        const pesanNotif = denda > 0 
            ? `📚 Buku "${peminjaman.title}" telah dikembalikan. Denda: Rp ${denda.toLocaleString('id-ID')}. Terima kasih!`
            : `📚 Buku "${peminjaman.title}" telah dikembalikan. Terima kasih!`;
        
        const notifAnggota = JSON.parse(localStorage.getItem('anggota_notifications')) || [];
        notifAnggota.unshift({
            id: Date.now(),
            message: pesanNotif,
            created_at: new Date().toLocaleString()
        });
        if (notifAnggota.length > 10) notifAnggota.pop();
        localStorage.setItem('anggota_notifications', JSON.stringify(notifAnggota));
        
        tutupModal();
        tampilkanPengembalian();
        
        if (denda > 0) {
            alert(`✅ Buku berhasil dikembalikan!\nDenda: Rp ${denda.toLocaleString('id-ID')}`);
        } else {
            alert('✅ Buku berhasil dikembalikan!');
        }
    }
}

function filterPengembalian(status) {
    filterSaatIni = status;
    tampilkanPengembalian();
}

function tutupModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Event pencarian
document.getElementById('cari').addEventListener('input', tampilkanPengembalian);

// Sinkron data dari anggota
sinkronDariAnggota();
tampilkanPengembalian();
</script>

@endsection