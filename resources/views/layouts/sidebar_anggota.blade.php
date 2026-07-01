<div class="flex flex-col h-full">
    <!-- Header -->
    <div class="p-5">
        <div class="flex items-center gap-2 text-xl font-bold">
            <i class="fas fa-user-graduate"></i>
            <span>Anggota Panel</span>
        </div>
        <div class="flex items-center gap-3 mt-4 pt-4 border-t border-blue-500">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
                <div class="text-xs text-blue-200">Anggota</div>
                <div class="text-xs text-blue-300 mt-0.5">NIM: {{ Auth::user()->nim }}</div>
            </div>
        </div>
    </div>

    <!-- Menu -->
    <div class="flex-1 px-5">
        <nav class="space-y-1">
            <div class="text-blue-200 text-xs mb-2">MENU UTAMA</div>
            
            <!-- Dashboard -->
            <a href="{{ url('/dashboard-anggota') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('dashboard-anggota') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i> Dashboard
            </a>
            
            <!-- Koleksi Buku -->
            <a href="{{ url('/dashboard-anggota/buku') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('dashboard-anggota/buku') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-book w-5"></i> Koleksi Buku
            </a>
            
            <!-- Peminjaman Saya -->
            <a href="{{ url('/dashboard-anggota/loans') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-600 transition {{ request()->is('dashboard-anggota/loans') ? 'bg-blue-600' : '' }}">
                <i class="fas fa-hand-holding-heart w-5"></i> Peminjaman Saya
            </a>
        </nav>
    </div>

    <!-- Tombol Keluar -->
    <div class="p-5 border-t border-blue-500">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded bg-red-500 hover:bg-red-600 transition">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </button>
        </form>
    </div>
</div>