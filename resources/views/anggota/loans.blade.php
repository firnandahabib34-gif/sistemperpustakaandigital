@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold">Peminjaman Saya</h1>
        <p class="text-gray-500 text-sm">Daftar buku yang Anda pinjam</p>
    </div>
    <button onclick="showNotifications()" class="bg-white px-4 py-2 rounded-lg shadow flex items-center gap-2 hover:bg-gray-50 transition relative">
        <i class="fas fa-bell"></i> Notifikasi
        <span id="notifBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5 min-w-[20px] text-center hidden">0</span>
    </button>
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
    <button onclick="filterLoans('menunggu_validasi')" id="filterMenungguValidasi" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
        ⏳ Menunggu Validasi
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
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Data dari database (dikirim dari controller)
let allLoans = @json($loans);
let currentFilter = 'semua';

// ============================================================
// NOTIFIKASI
// ============================================================

let notifications = [];

// Ambil notifikasi dari database
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

// ============================================================
// PEMINJAMAN
// ============================================================

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
        else if (currentFilter === 'menunggu_validasi') message = '⏳ Tidak ada peminjaman menunggu validasi';
        else if (currentFilter === 'dikembalikan') message = '✅ Belum ada riwayat pengembalian';
        else if (currentFilter === 'ditolak') message = '❌ Tidak ada peminjaman yang ditolak';
        else message = '📭 Tidak ada data';
        
        grid.innerHTML = `<div class="col-span-3 text-center text-gray-500 py-10">${message}</div>`;
        return;
    }
    
    grid.innerHTML = filtered.map(loan => {
        const statusConfig = {
            'menunggu': { bg: 'bg-yellow-100', text: 'text-yellow-700', icon: '⏳', label: 'Menunggu Persetujuan', border: 'border-yellow-300' },
            'dipinjam': { bg: 'bg-blue-100', text: 'text-blue-700', icon: '📖', label: 'Sedang Dipinjam', border: 'border-blue-300' },
            'menunggu_validasi': { bg: 'bg-purple-100', text: 'text-purple-700', icon: '⏳', label: 'Menunggu Konfirmasi Admin', border: 'border-purple-300' },
            'dikembalikan': { bg: 'bg-green-100', text: 'text-green-700', icon: '✅', label: 'Sudah Dikembalikan', border: 'border-green-300' },
            'ditolak': { bg: 'bg-red-100', text: 'text-red-700', icon: '❌', label: 'Ditolak', border: 'border-red-300' }
        };
        
        const config = statusConfig[loan.status] || statusConfig['menunggu'];
        const book = loan.book || {};
        
        // Hitung keterlambatan jika status dipinjam
        let lateInfo = '';
        if (loan.status === 'dipinjam') {
            const dueDate = new Date(loan.due_date);
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
        if (loan.fine > 0) {
            dendaInfo = `<div class="mt-2 p-2 bg-red-50 rounded-lg">
                            <span class="text-red-600 text-sm">💰 Denda: Rp ${loan.fine.toLocaleString('id-ID')}</span>
                         </div>`;
        }
        
       return `
    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition border-l-4 ${config.border} overflow-hidden">
        <div class="p-5">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-800">${escapeHtml(book.judul || 'Buku tidak ditemukan')}</h3>
                    <p class="text-sm text-gray-500 mt-1">ID Peminjaman: #${loan.id}</p>
                </div>
                <div class="px-3 py-1 rounded-full text-xs font-semibold ${config.bg} ${config.text}">
                    ${config.icon} ${config.label}
                </div>
            </div>
            
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-calendar-alt w-4 text-gray-400"></i>
                    <span>Pinjam: ${new Date(loan.borrow_date).toLocaleDateString('id-ID')}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-hourglass-end w-4 text-gray-400"></i>
                    <span>Jatuh Tempo: ${new Date(loan.due_date).toLocaleDateString('id-ID')}</span>
                </div>
                ${loan.return_date ? `
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-undo-alt w-4 text-gray-400"></i>
                    <span>Dikembalikan: ${new Date(loan.return_date).toLocaleDateString('id-ID')}</span>
                </div>
                ` : `
                <div class="flex items-center gap-2 text-gray-400">
                    <i class="fas fa-undo-alt w-4"></i>
                    <span>Belum dikembalikan</span>
                </div>
                `}
                ${loan.fine > 0 ? `
                <div class="flex items-center gap-2 text-red-600 font-semibold">
                    <i class="fas fa-money-bill w-4"></i>
                    <span>Denda: Rp ${loan.fine.toLocaleString('id-ID')}</span>
                </div>
                ` : ''}
            </div>
            
            ${lateInfo}
            ${dendaInfo}
            
            ${loan.status === 'dipinjam' ? `
            <div class="mt-3 flex gap-2">
                ${loan.extend_status === null || loan.extend_status === '' ? `
                <form method="POST" action="{{ url('pinjam') }}/${loan.id}/extend" class="flex-1">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg text-sm font-medium transition" onclick="return confirm('Ajukan perpanjangan 7 hari?')">
                        📅 Ajukan Perpanjang
                    </button>
                </form>
                ` : ''}
                
                ${loan.extend_status === 'menunggu' ? `
                <div class="flex-1 text-center text-sm text-yellow-600 bg-yellow-50 py-2 rounded-lg">
                    ⏳ Menunggu admin
                </div>
                ` : ''}
                
                ${loan.extend_status === 'disetujui' ? `
                <div class="flex-1 text-center text-sm text-green-600 bg-green-50 py-2 rounded-lg">
                    ✅ Perpanjangan Disetujui
                </div>
                ` : ''}
                
                ${loan.extend_status === 'ditolak' ? `
                <div class="flex-1 text-center text-sm text-red-600 bg-red-50 py-2 rounded-lg">
                    ❌ Perpanjangan Ditolak
                </div>
                ` : ''}
                
                <!-- Tombol Kembalikan -->
                <form method="POST" action="{{ url('pinjam') }}/${loan.id}/confirm-return" class="flex-1">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" class="w-full bg-green-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-medium transition" onclick="return confirm('Yakin sudah mengembalikan buku?')">
                        📤 Kembalikan
                    </button>
                </form>
            </div>
            ` : ''}
            
            ${loan.extended_at ? `
            <div class="flex items-center gap-2 text-blue-600 text-sm">
                <i class="fas fa-clock w-4"></i>
                <span>Diperpanjang: ${new Date(loan.extended_at).toLocaleDateString('id-ID')}</span>
            </div>
            ` : ''}

            ${loan.status === 'menunggu_validasi' ? `
            <div class="mt-4 pt-3 border-t text-center text-sm text-gray-500">
                ⏳ Menunggu validasi petugas perpustakaan
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
    
    const filters = ['semua', 'menunggu', 'dipinjam', 'menunggu_validasi', 'dikembalikan', 'ditolak'];
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

// Load data awal
renderLoans();
loadNotifications();

// Refresh notifikasi setiap 10 detik
setInterval(() => {
    loadNotifications();
}, 10000);
</script>

@endsection