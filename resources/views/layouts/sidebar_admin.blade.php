<aside class="w-64 bg-blue-700 text-white flex-shrink-0 min-h-screen flex flex-col">
    <div class="p-5 flex-1">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xl font-bold">
                <i class="fas fa-user-shield"></i>
                <span>Admin Panel</span>
            </div>
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-blue-500">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">Administrator</div>
                    <div class="text-xs text-blue-200">Admin</div>
                </div>
            </div>
        </div>

        <nav class="space-y-1">
            <div class="text-white-200 text-xs mb-2">MAIN MENU</div>
            
            <a href="/dashboard-admin" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('dashboard-admin') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i> Dashboard
            </a>
            
            <a href="/admin/books" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('admin/books') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-book w-5"></i> Kelola Buku
            </a>
            
            <a href="/admin/anggota" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('admin/anggota') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-users w-5"></i> Kelola Anggota
            </a>
            
            <a href="/admin/loans" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('admin/loans') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-hand-holding-heart w-5"></i> Peminjaman
            </a>

            <a href="/admin/pengembalian" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('admin/pengembalian') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-undo-alt w-5"></i> Pengembalian
            </a>

            <a href="/admin/kategori" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('admin/kategori') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-tags w-5"></i> Kelola Kategori
            </a>

            <a href="/admin/laporan" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('admin/laporan') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-chart-line w-5"></i> Laporan
            </a>

        </nav>
    </div>
    
    <!-- Tombol Keluar di bawah (paling bawah) -->
    <div class="p-5 border-t border-blue-500">
        <a href="/login" class="flex items-center justify-center gap-2 px-3 py-2 rounded bg-red-500 hover:bg-red-600 transition w-full">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</aside>