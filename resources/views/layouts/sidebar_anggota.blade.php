<aside class="w-64 bg-blue-700 text-white flex-shrink-0 min-h-screen flex flex-col">
    <div class="p-5 flex-1">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xl font-bold">
                <i class="fas fa-user-graduate"></i>
                <span>Anggota Panel</span>
            </div>
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-blue-500">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold" id="userName">Mahasiswa</div>
                    <div class="text-xs text-blue-200">Anggota Aktif</div>
                </div>
            </div>
        </div>

        <nav class="space-y-1">
            <div class="text-blue-200 text-xs mb-2">MENU UTAMA</div>
            
            <a href="/dashboard-anggota" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('dashboard-anggota') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i> Dashboard
            </a>
            
            <a href="/dashboard-anggota/buku" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('dashboard-anggota/buku') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-book w-5"></i> Koleksi Buku
            </a>
            
            <a href="/dashboard-anggota/loans" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('dashboard-anggota/loans') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-hand-holding-heart w-5"></i> Peminjaman
            </a>
        </nav>
    </div>
    
    <!-- Tombol Keluar di bawah (mt-auto) -->
    <div class="p-5 border-t border-blue-500">
        <a href="/login" class="flex items-center gap-3 px-3 py-2 rounded bg-red-500 hover:bg-red-600 transition w-full">
            <i class="fas fa-sign-out-alt w-5"></i> Keluar
        </a>
    </div>
</aside>

<script>
// Ambil nama user dari localStorage
const loggedIn = JSON.parse(localStorage.getItem('logged_in'));
if (loggedIn && loggedIn.name) {
    const userNameSpan = document.getElementById('userName');
    if (userNameSpan) userNameSpan.innerText = loggedIn.name;
}
</script>