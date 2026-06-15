@extends('layouts.app')

@section('title', 'Dashboard Anggota')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold">Dashboard Anggota</h1>
        <p class="text-gray-500 text-sm">Selamat datang di perpustakaan digital</p>
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
    
    <a href="/dashboard-anggota/loans" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <div class="flex items-center space-x-4">
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-hand-holding-heart text-green-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">Peminjaman Saya</h3>
                <p class="text-gray-500 text-sm">Lihat status peminjaman</p>
            </div>
        </div>
    </a>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

let notifications = [];

// ============================================================
// NOTIFIKASI (SAMA SEPERTI DI KOLEKSI BUKU)
// ============================================================

// Ambil notifikasi dari database
async function loadNotifications() {
    try {
        const response = await fetch('/api/anggota/notifications');
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

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// ============================================================
// STATISTIK
// ============================================================

async function loadStats() {
    try {
        const response = await fetch('/api/anggota/loans');
        const loans = await response.json();
        
        const dipinjam = loans.filter(l => l.status === 'dipinjam').length;
        const menunggu = loans.filter(l => l.status === 'menunggu').length;
        const totalDenda = loans.reduce((sum, l) => sum + (l.fine || 0), 0);
        
        const totalDipinjamEl = document.getElementById('totalDipinjam');
        const totalMenungguEl = document.getElementById('totalMenunggu');
        const totalDendaEl = document.getElementById('totalDenda');
        
        if (totalDipinjamEl) totalDipinjamEl.innerText = dipinjam;
        if (totalMenungguEl) totalMenungguEl.innerText = menunggu;
        if (totalDendaEl) totalDendaEl.innerHTML = `Rp ${totalDenda.toLocaleString('id-ID')}`;
    } catch (error) {
        console.error('Gagal load statistik:', error);
    }
}

// ============================================================
// INITIAL LOAD & INTERVAL
// ============================================================

loadNotifications();
loadStats();

// Refresh notifikasi dan statistik setiap 10 detik
setInterval(() => {
    loadNotifications();
    loadStats();
}, 10000);
</script>

@endsection