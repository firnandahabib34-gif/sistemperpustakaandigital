<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
   // Anggota ajukan peminjaman (via AJAX/JSON)
public function store(Request $request, $book_id = null)
{
    // Ambil book_id dari parameter atau dari request
    $bookId = $book_id ?? $request->book_id;
    
    $book = Book::findOrFail($bookId);

    if (!$book->isTersedia()) {
        return response()->json([
            'success' => false,
            'message' => 'Buku tidak tersedia'
        ], 400);
    }

    $existing = Loan::where('user_id', Auth::id())
                    ->where('book_id', $book->id)
                    ->whereIn('status', ['menunggu', 'dipinjam'])
                    ->first();

    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'Anda sudah meminjam/mengajukan buku ini'
        ], 400);
    }

    $book->kurangiStok();

    Loan::create([
        'user_id' => Auth::id(),
        'book_id' => $book->id,
        'borrow_date' => now(),
        'due_date' => now()->addDays(7),
        'status' => 'menunggu'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Peminjaman berhasil diajukan'
    ]);
}

    // Admin melihat daftar peminjaman
    public function index()
    {
        $loans = Loan::with(['user', 'book'])->orderBy('created_at', 'desc')->get();
        return view('admin.loans.index', compact('loans'));
    }

    // Admin menyetujui
    public function approve($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->status = 'dipinjam';
        $loan->admin_id = Auth::id();
        $loan->save();

        return redirect()->back()->with('success', 'Peminjaman disetujui');
    }

    // Admin menolak (kembalikan stok)
    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        
        $book = Book::find($loan->book_id);
        if ($book) $book->tambahStok();

        $loan->status = 'ditolak';
        $loan->admin_id = Auth::id();
        $loan->save();

        return redirect()->back()->with('success', 'Peminjaman ditolak');
    }

    // Admin proses pengembalian
    public function return($id)
    {
        $loan = Loan::findOrFail($id);
        
        $fine = $loan->calculateFine();

        $loan->status = 'dikembalikan';
        $loan->return_date = now();
        $loan->fine = $fine;
        $loan->admin_id = Auth::id();
        $loan->save();

        $book = Book::find($loan->book_id);
        if ($book) $book->tambahStok();

        $message = $fine > 0 
            ? "Buku dikembalikan dengan denda Rp " . number_format($fine, 0, ',', '.')
            : "Buku berhasil dikembalikan";

        return redirect()->back()->with('success', $message);
    }

    // Anggota lihat riwayat
    public function history()
    {
        $loans = Loan::where('user_id', Auth::id())
                     ->with('book')
                     ->orderBy('created_at', 'desc')
                     ->get();
        return view('anggota.loans', compact('loans'));
    }
}