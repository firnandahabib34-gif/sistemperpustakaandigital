@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold">Peminjaman Buku</h1>
    <p class="text-gray-500 text-sm">Kelola peminjaman buku oleh anggota</p>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Anggota</th>
                <th class="px-6 py-3 text-left">Buku</th>
                <th class="px-6 py-3 text-left">Tanggal Pinjam</th>
                <th class="px-6 py-3 text-left">Jatuh Tempo</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($loans as $loan)
            <tr>
                <td class="px-6 py-3">{{ $loan->user->name ?? '-' }}</td>
                <td class="px-6 py-3">{{ $loan->book->judul ?? '-' }}</td>
                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}</td>
                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}</td>
                <td class="px-6 py-3">
                    @if($loan->status == 'menunggu')
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">⏳ Menunggu</span>
                    @elseif($loan->status == 'dipinjam')
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm">📖 Dipinjam</span>
                    @elseif($loan->status == 'menunggu_validasi')
                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-sm">⏳ Menunggu Konfirmasi</span>
                    @elseif($loan->status == 'dikembalikan')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">✅ Dikembalikan</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-sm">❌ Ditolak</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-center whitespace-nowrap">
                    @if($loan->status == 'menunggu')
                        <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">✅ Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}" class="inline ml-1">
                            @csrf
                            @method('PATCH')
                            <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">❌ Tolak</button>
                        </form>
                    @elseif($loan->status == 'menunggu_validasi')
                        <button onclick="kembalikanBuku({{ $loan->id }})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                            ✅ Konfirmasi Pengembalian
                        </button>
                    @else
                        <span class="text-gray-400 text-sm">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function kembalikanBuku(id) {
    if (confirm('Yakin anggota sudah mengembalikan buku secara fisik?')) {
        try {
            const response = await fetch(`/admin/loans/${id}/return`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const result = await response.json();
            
            if (response.ok) {
                alert('✅ ' + result.message);
                location.reload();
            } else {
                alert('❌ ' + (result.message || 'Gagal memproses pengembalian'));
            }
        } catch (error) {
            alert('❌ Terjadi kesalahan: ' + error.message);
        }
    }
}
</script>

@endsection