@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Peminjaman Buku</h1>
    <p class="text-gray-500">Kelola peminjaman buku oleh anggota</p>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Anggota</th>
                <th class="px-6 py-3 text-left">Buku</th>
                <th class="px-6 py-3 text-left">Tgl Pinjam</th>
                <th class="px-6 py-3 text-left">Jatuh Tempo</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($loans as $loan)
            <tr>
                <td class="px-6 py-3">{{ $loan->user->name }}</td>
                <td class="px-6 py-3">{{ $loan->book->judul }}</td>
                <td class="px-6 py-3">{{ $loan->borrow_date->format('d/m/Y') }}</td>
                <td class="px-6 py-3">{{ $loan->due_date->format('d/m/Y') }}</td>
                <td class="px-6 py-3">
                    @if($loan->status == 'menunggu')
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">⏳ Menunggu</span>
                    @elseif($loan->status == 'dipinjam')
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">📖 Dipinjam</span>
                    @elseif($loan->status == 'dikembalikan')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded">✅ Dikembalikan</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded">❌ Ditolak</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-center">
                    @if($loan->status == 'menunggu')
                        <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button class="bg-green-500 text-white px-3 py-1 rounded text-sm">✅ Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button class="bg-red-500 text-white px-3 py-1 rounded text-sm">❌ Tolak</button>
                        </form>
                    @elseif($loan->status == 'dipinjam')
                        <form method="POST" action="{{ route('admin.loans.return', $loan->id) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button class="bg-blue-500 text-white px-3 py-1 rounded text-sm">📖 Kembalikan</button>
                        </form>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection