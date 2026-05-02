<aside class="w-64 bg-gray-900 text-white flex-shrink-0 min-h-screen">
    <div class="p-5">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xl font-bold">
                <i class="fas fa-user-graduate"></i>
                <span>Anggota Panel</span>
            </div>
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-700">
                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold" id="userName">Mahasiswa</div>
                    <div class="text-xs text-gray-400">Anggota Aktif</div>
                </div>
            </div>
        </div>

        <nav class="space-y-1">
            <div class="text-gray-400 text-xs mb-2">MENU UTAMA</div>
            
            <a href="/dashboard-anggota" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('dashboard-anggota') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i> Dashboard
            </a>
            
            <a href="/dashboard-anggota/buku" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('dashboard-anggota/buku') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-book w-5"></i> Koleksi Buku
            </a>
            
            <!-- Sesudah (halaman terpisah) -->
            <a href="/dashboard-anggota/loans" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('dashboard-anggota/loans') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-hand-holding-heart w-5"></i> Peminjaman Saya
            </a>
            
            <div class="border-t border-gray-700 my-3"></div>
            
            <div class="text-gray-400 text-xs mb-2">LAINNYA</div>
            
            <a href="/login" class="flex items-center gap-3 px-3 py-2 rounded bg-red-500 hover:bg-red-600 transition mt-2">
                <i class="fas fa-sign-out-alt w-5"></i> Keluar
            </a>
        </nav>
    </div>
</aside>

<script>
// Ambil nama user dari localStorage
const loggedIn = JSON.parse(localStorage.getItem('logged_in'));
if (loggedIn && loggedIn.name) {
    const userNameSpan = document.getElementById('userName');
    if (userNameSpan) userNameSpan.innerText = loggedIn.name;
}

// Fungsi showMyLoans akan dipanggil dari halaman
window.showMyLoans = function() {
    // Trigger event untuk membuka modal di halaman yang aktif
    const event = new CustomEvent('showMyLoans');
    window.dispatchEvent(event);
};
</script>