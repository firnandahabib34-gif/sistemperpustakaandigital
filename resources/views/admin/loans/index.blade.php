@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold">Peminjaman Buku</h1>
    <p class="text-gray-500 text-sm">Kelola peminjaman buku oleh anggota</p>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1300px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">ID Pinjam</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Anggota</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Judul Buku</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Kode</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">ISBN</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Rak</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Tanggal Pinjam</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Jatuh Tempo</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Tanggal Kembali</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Denda</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Status Denda</th> <!-- ✅ BARU -->
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Status</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 whitespace-nowrap">Perpanjang</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($loans as $loan)
                <tr>
                    <td class="px-3 py-2 text-sm font-mono whitespace-nowrap">#{{ $loan->id }}</td>
                    <td class="px-3 py-2 text-sm whitespace-nowrap">{{ $loan->user->name ?? '-' }}</td>
                    <td class="px-3 py-2 text-sm font-medium whitespace-nowrap">{{ $loan->book->judul ?? '-' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-500 whitespace-nowrap">{{ $loan->book->kode_buku ?? '-' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-500 whitespace-nowrap">{{ $loan->book->isbn ?? '-' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-500 whitespace-nowrap">{{ $loan->book->lokasi_rak ?? '-' }}</td>
                    <td class="px-3 py-2 text-sm whitespace-nowrap">{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}</td>
                    <td class="px-3 py-2 text-sm whitespace-nowrap">{{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}</td>
                    <td class="px-3 py-2 text-sm whitespace-nowrap">
                        {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-3 py-2 text-sm font-semibold whitespace-nowrap {{ $loan->fine > 0 ? 'text-red-600' : 'text-gray-500' }}">
                        {{ $loan->fine > 0 ? 'Rp ' . number_format($loan->fine, 0, ',', '.') : '-' }}
                    </td>
                    
                    <!-- ✅ BARU: KOLOM STATUS DENDA -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if($loan->fine > 0)
                            @if($loan->fine_status == 'belum_bayar')
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-semibold">
                                    🔴 Belum Bayar
                                </span>
                            @elseif($loan->fine_status == 'sudah_bayar')
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-semibold">
                                    🟢 Sudah Bayar
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    
                    <!-- KOLOM STATUS PEMINJAMAN -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if($loan->status == 'menunggu')
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs">Menunggu</span>
                        @elseif($loan->status == 'dipinjam')
                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">Dipinjam</span>
                        @elseif($loan->status == 'menunggu_validasi')
                            <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs">Konfirmasi</span>
                        @elseif($loan->status == 'dikembalikan')
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">Kembali</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs">Ditolak</span>
                        @endif
                    </td>
                    
                    <!-- KOLOM PERPANJANG -->
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if($loan->extend_status === 'menunggu')
                            <form method="POST" action="{{ route('admin.loans.approve-extend', $loan->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-0.5 rounded text-xs" title="Setujui Perpanjangan">✅</button>
                            </form>
                            <form method="POST" action="{{ route('admin.loans.reject-extend', $loan->id) }}" class="inline ml-0.5">
                                @csrf
                                @method('PATCH')
                                <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-0.5 rounded text-xs" title="Tolak Perpanjangan">❌</button>
                            </form>
                        @elseif($loan->extend_status === 'disetujui')
                            <span class="text-green-600 text-xs font-semibold">✅</span>
                        @elseif($loan->extend_status === 'ditolak')
                            <span class="text-red-600 text-xs font-semibold">❌</span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    
                    <!-- ✅ BARU: KOLOM AKSI (DENGAN TOMBOL BAYAR) -->
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        <!-- Approve/Reject Peminjaman -->
                        @if($loan->status == 'menunggu')
                            <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-0.5 rounded text-xs">✅</button>
                            </form>
                            <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}" class="inline ml-0.5">
                                @csrf
                                @method('PATCH')
                                <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-0.5 rounded text-xs">❌</button>
                            </form>
                        @elseif($loan->status == 'menunggu_validasi')
                            <button onclick="kembalikanBuku({{ $loan->id }})" class="bg-green-500 hover:bg-green-600 text-white px-2 py-0.5 rounded text-xs">
                                Kembali
                            </button>
                        @endif
                        
                        <!-- ✅ BARU: TOMBOL VALIDASI PEMBAYARAN DENDA -->
                        @if($loan->fine > 0 && $loan->fine_status == 'belum_bayar')
                            <form method="POST" action="{{ route('admin.fines.pay', $loan->id) }}" class="inline ml-0.5">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-0.5 rounded text-xs"
                                        onclick="return confirm('Yakin ingin memvalidasi pembayaran denda Rp {{ number_format($loan->fine, 0, ',', '.') }}?')">
                                    💰 Bayar
                                </button>
                            </form>
                        @endif
                        
                        @if($loan->status != 'menunggu' && $loan->status != 'menunggu_validasi' && !($loan->fine > 0 && $loan->fine_status == 'belum_bayar'))
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function kembalikanBuku(id) {
    if (confirm('Yakin anggota sudah mengembalikan buku secara fisik?')) {
        try {
            const response = await fetch("{{ url('admin/loans') }}/" + id + "/return", {
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