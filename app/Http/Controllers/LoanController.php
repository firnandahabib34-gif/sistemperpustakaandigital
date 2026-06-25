<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    // Anggota ajukan peminjaman (via AJAX)
    public function store(Request $request, $book_id = null)
    {
        $bookId = $book_id ?? $request->book_id;
        $book = Book::findOrFail($bookId);

        if ($book->stok <= 0) {
            return response()->json(['success' => false, 'message' => 'Stok buku habis'], 400);
        }

        $existing = Loan::where('user_id', Auth::id())
                        ->where('book_id', $book->id)
                        ->whereIn('status', ['menunggu', 'dipinjam', 'menunggu_validasi'])
                        ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Anda sudah meminjam/mengajukan buku ini'], 400);
        }

        $book->stok -= 1;
        $book->save();

        Loan::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'borrow_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'menunggu',
            'anggota_confirmed' => 0,
            'fine' => 0
        ]);

        return response()->json(['success' => true, 'message' => 'Peminjaman berhasil diajukan']);
    }

    // Admin melihat daftar peminjaman
    public function index()
    {
        $loans = Loan::with(['user', 'book'])->orderBy('created_at', 'desc')->get();
        return view('admin.loans.index', compact('loans'));
    }

    // Admin melihat daftar pengembalian yang menunggu validasi
    public function pengembalian()
    {
        $loans = Loan::whereIn('status', ['menunggu_validasi', 'dikembalikan'])
                     ->with(['user', 'book'])
                     ->orderBy('created_at', 'desc')
                     ->get();
        return view('admin.pengembalian.index', compact('loans'));
    }

    // Admin menyetujui peminjaman
    public function approve($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->status = 'dipinjam';
        $loan->admin_id = Auth::id();
        $loan->anggota_confirmed = 0;
        $loan->save();

        Notification::send(
            $loan->user_id,
            '✅ Peminjaman Disetujui',
            "Peminjaman buku '{$loan->book->judul}' telah disetujui. Jatuh tempo: " . now()->addDays(7)->format('d/m/Y')
        );

        return redirect()->back()->with('success', 'Peminjaman disetujui');
    }

    // Admin menolak peminjaman (kembalikan stok)
    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        
        $book = Book::find($loan->book_id);
        if ($book) {
            $book->stok += 1;
            $book->save();
        }

        $loan->status = 'ditolak';
        $loan->admin_id = Auth::id();
        $loan->save();

        Notification::send(
            $loan->user_id,
            '❌ Peminjaman Ditolak',
            "Peminjaman buku '{$loan->book->judul}' ditolak oleh admin."
        );

        return redirect()->back()->with('success', 'Peminjaman ditolak');
    }

    // Anggota konfirmasi sudah mengembalikan buku
    public function confirmReturn($id)
    {
        $loan = Loan::where('user_id', auth()->id())
                    ->where('id', $id)
                    ->where('status', 'dipinjam')
                    ->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Peminjaman tidak ditemukan atau sudah diproses');
        }

        $loan->anggota_confirmed = 1;
        $loan->status = 'menunggu_validasi';
        $loan->save();

        return redirect()->back()->with('success', 'Konfirmasi berhasil. Menunggu validasi admin.');
    }

    // 🔥 HITUNG DENDA
private function calculateFine($loan)
{
    $today = now();
    $dueDate = $loan->due_date;

    if ($today > $dueDate) {
        // 🔥 PAKAI (int) AGAR HASILNYA INTEGER
        $daysLate = (int) $dueDate->diffInDays($today);
        return $daysLate * 2000;
    }

    return 0;
}

    // Admin validasi pengembalian (hanya bisa jika status menunggu_validasi)
    public function returnLoan($id)
    {
        try {
            $loan = Loan::with('book')->findOrFail($id);
            
            if ($loan->status !== 'menunggu_validasi') {
                return response()->json(['message' => 'Status tidak valid untuk pengembalian'], 400);
            }
            
            // 🔥 HITUNG DENDA
            $fine = $this->calculateFine($loan);
            
            // 🔥 SIMPAN DENDA KE DATABASE (POSITIF)
            $loan->fine = $fine;
            
            $loan->status = 'dikembalikan';
            $loan->return_date = now();
            $loan->admin_id = Auth::id();
            $loan->save();
            
            // Kembalikan stok
            $book = Book::find($loan->book_id);
            if ($book) {
                $book->stok += 1;
                $book->save();
            }
            
            // Kirim notifikasi ke anggota
            $message = $fine > 0 
                ? "Buku '{$loan->book->judul}' telah dikembalikan dengan denda Rp " . number_format($fine, 0, ',', '.')
                : "Buku '{$loan->book->judul}' telah dikembalikan. Terima kasih!";
            
            Notification::send($loan->user_id, '📚 Pengembalian Buku', $message);
            
            return response()->json([
                'success' => true,
                'message' => $fine > 0 ? "Buku dikembalikan dengan denda Rp " . number_format($fine, 0, ',', '.') : 'Buku berhasil dikembalikan'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Anggota lihat riwayat peminjaman
    public function history()
    {
        $loans = Loan::where('user_id', Auth::id())
                     ->with('book')
                     ->orderBy('created_at', 'desc')
                     ->get();
        return view('anggota.loans', compact('loans'));
    }
}