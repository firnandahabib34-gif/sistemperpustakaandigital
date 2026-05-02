<aside class="w-64 bg-gray-900 text-white flex-shrink-0 min-h-screen">
    <div class="p-5">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xl font-bold">
                <i class="fas fa-user-shield"></i>
                <span>Admin Panel</span>
            </div>
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-700">
                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">Administrator</div>
                    <div class="text-xs text-gray-400">Admin</div>
                </div>
            </div>
        </div>

        <nav class="space-y-1">
            <div class="text-gray-400 text-xs mb-2">MAIN MENU</div>
            
            <a href="/dashboard-admin" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('dashboard-admin') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i> Dashboard
            </a>
            
            <a href="/admin/books" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('admin/books') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-book w-5"></i> Kelola Buku
            </a>
            
            <a href="/admin/anggota" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('admin/anggota') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-users w-5"></i> Kelola Anggota
            </a>
            
            <a href="/admin/loans" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('admin/loans') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-hand-holding-heart w-5"></i> Peminjaman
            </a>

            <a href="/admin/pengembalian" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('admin/pengembalian') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-undo-alt w-5"></i> Pengembalian
            </a>
            
            <div class="border-t border-gray-700 my-3"></div>
            
            <div class="text-gray-400 text-xs mb-2">LAINNYA</div>
            
            <a href="/login" class="flex items-center gap-3 px-3 py-2 rounded bg-red-500 hover:bg-red-600 transition mt-2">
                <i class="fas fa-sign-out-alt w-5"></i> Keluar
            </a>
        </nav>
    </div>
</aside>