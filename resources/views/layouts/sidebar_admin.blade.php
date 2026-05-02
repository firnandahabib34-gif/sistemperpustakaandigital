<aside class="w-64 bg-gray-900 text-white min-h-screen p-5">

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xl font-bold">
            <i class="fas fa-chalkboard-user"></i>
            <span>Admin Panel</span>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                AD
            </div>
            <div>
                <div class="text-sm font-semibold">Admin</div>
                <div class="text-xs text-gray-400">Administrator</div>
            </div>
        </div>
    </div>

    <!-- Menu -->
    <nav class="space-y-2 text-sm">

        <div class="text-gray-400 text-xs mb-2">MAIN MENU</div>

        <a href="/dashboard-admin" class="block px-3 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>

        <a href="{{ route('admin.books') }}" class="block px-3 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-book mr-2"></i> Kelola Buku
        </a>

        <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-users mr-2"></i> Kelola Anggota
        </a>

        <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-hand-holding-heart mr-2"></i> Peminjaman
        </a>

        <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-undo-alt mr-2"></i> Pengembalian
        </a>

        <div class="text-gray-400 text-xs mt-4">LAINNYA</div>

        <a href="/login" class="block px-3 py-2 rounded bg-red-500 hover:bg-red-600 mt-2">
            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
        </a>

    </nav>

</aside>