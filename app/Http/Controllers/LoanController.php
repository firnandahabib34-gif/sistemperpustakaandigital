<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    // Anggota ajukan peminjaman (via AJAX)
    public function store(Request $request, $book_id = null)
    {
        $bookId = $book_id ?? $request->book_id;
        $book = Book::findOrFail($bookId);

        // ===========================
        // CEK STOK BUKU
        // ===========================
        if ($book->stok <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stok buku habis'
            ], 400);
        }

        // ===========================
        // BATAS MAKSIMAL PEMINJAMAN
        // ===========================
        $jumlahPinjaman = Loan::where('user_id', Auth::id())
            ->whereIn('status', [
                'menunggu',
                'dipinjam',
                'menunggu_validasi'
            ])
            ->count();

        if ($jumlahPinjaman >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mencapai batas maksimal peminjaman (3 buku).'
            ], 400);
        }

        // ===========================
        // CEK BUKU YANG SAMA
        // ===========================
        $existing = Loan::where('user_id', Auth::id())
                        ->where('book_id', $book->id)
                        ->whereIn('status', [
                            'menunggu',
                            'dipinjam',
                            'menunggu_validasi'
                        ])
                        ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah meminjam/mengajukan buku ini.'
            ], 400);
        }

        // ===========================
        // TRANSACTION DATABASE
        // ===========================
        try {
            DB::transaction(function () use ($book) {
                $book->stok -= 1;
                $book->save();

                Loan::create([
                    'user_id' => Auth::id(),
                    'book_id' => $book->id,
                    'borrow_date' => now(),
                    'due_date' => now()->addDays(7),
                    'status' => 'menunggu',
                    'anggota_confirmed' => 0,
                    'fine' => 0,
                    'fine_status' => 'sudah_bayar',
                    'fine_paid_at' => null,
                    'fine_paid_by' => null,
                    'extended_count' => 0
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diajukan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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
        try {
            DB::transaction(function () use ($id) {
                $loan = Loan::findOrFail($id);
                $loan->status = 'dipinjam';
                $loan->admin_id = Auth::id();
                $loan->anggota_confirmed = 0;
                $loan->save();

                // ===== REVISI: Gunakan due_date yang sudah ada =====
                Notification::send(
                    $loan->user_id,
                    '✅ Peminjaman Disetujui',
                    "Peminjaman buku '{$loan->book->judul}' telah disetujui oleh admin. Jatuh tempo: " . 
                    $loan->due_date->format('d/m/Y')
                );
            });

            return redirect()->back()->with('success', 'Peminjaman disetujui');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Admin menolak peminjaman (kembalikan stok)
    public function reject($id)
    {
        try {
            DB::transaction(function () use ($id) {
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
            });

            return redirect()->back()->with('success', 'Peminjaman ditolak');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

    // Hitung denda
    private function calculateFine($loan)
    {
        $today = now();
        $dueDate = $loan->due_date;

        if ($today > $dueDate) {
            $daysLate = (int) $dueDate->diffInDays($today);
            return $daysLate * 2000;
        }

        return 0;
    }

    // Admin validasi pengembalian
    public function returnLoan($id)
    {
        try {
            $loan = Loan::with('book')->findOrFail($id);
            
            if ($loan->status !== 'menunggu_validasi') {
                return response()->json(['message' => 'Status tidak valid untuk pengembalian'], 400);
            }
            
            // ===========================
            // TRANSACTION DATABASE
            // ===========================
            DB::transaction(function () use ($loan) {
                $fine = $this->calculateFine($loan);
                
                $loan->fine = $fine;
                
                if ($fine > 0) {
                    $loan->fine_status = 'belum_bayar';
                } else {
                    $loan->fine_status = 'sudah_bayar';
                }
                
                $loan->status = 'dikembalikan';
                $loan->return_date = now();
                $loan->admin_id = Auth::id();
                $loan->save();
                
                $book = Book::find($loan->book_id);
                if ($book) {
                    $book->stok += 1;
                    $book->save();
                }
            });

            // Ambil data terbaru setelah transaction
            $loan->refresh();
            $fine = $loan->fine;
            
            if ($fine > 0) {
                $message = "Buku '{$loan->book->judul}' telah dikembalikan dengan denda Rp " . 
                           number_format($fine, 0, ',', '.') . 
                           ". Silakan segera lakukan pembayaran denda ke admin.";
            } else {
                $message = "Buku '{$loan->book->judul}' telah dikembalikan. Terima kasih!";
            }
            
            Notification::send($loan->user_id, '📚 Pengembalian Buku', $message);
            
            return response()->json([
                'success' => true,
                'message' => $fine > 0 ? "Buku dikembalikan dengan denda Rp " . number_format($fine, 0, ',', '.') : 'Buku berhasil dikembalikan',
                'fine' => $fine,
                'fine_status' => $loan->fine_status
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    // PAYFINE DENGAN VALIDASI TAMBAHAN
    // ============================================================
    public function payFine($id)
    {
        $loan = Loan::findOrFail($id);

        // ===== REVISI: Cek apakah buku sudah dikembalikan =====
        if ($loan->status != 'dikembalikan') {
            return redirect()->back()
                ->with('error', 'Buku belum selesai divalidasi pengembaliannya.');
        }

        // Cek apakah ada denda
        if ($loan->fine <= 0) {
            return redirect()->back()
                ->with('error', 'Tidak ada denda yang harus dibayar.');
        }

        // Cek apakah sudah lunas
        if ($loan->fine_status == 'sudah_bayar') {
            return redirect()->back()
                ->with('error', 'Denda sudah dibayar.');
        }

        try {
            // ===========================
            // TRANSACTION DATABASE
            // ===========================
            DB::transaction(function () use ($loan) {
                $loan->fine_status = 'sudah_bayar';
                $loan->fine_paid_at = now();
                $loan->fine_paid_by = Auth::id();
                $loan->save();
            });

            // ===== NOTIFIKASI LEBIH INFORMATIF =====
            $adminName = Auth::user()->name;
            $fineAmount = number_format($loan->fine, 0, ',', '.');
            
            Notification::send(
                $loan->user_id,
                '💰 Pembayaran Denda',
                "Denda sebesar Rp {$fineAmount} telah dikonfirmasi oleh admin {$adminName} pada " . 
                now()->format('d/m/Y H:i') . ". Terima kasih!"
            );

            // ===== NOTIFIKASI KE ADMIN LAIN =====
            $admins = User::where('role', 'admin')->where('id', '!=', Auth::id())->get();
            foreach ($admins as $admin) {
                Notification::send(
                    $admin->id,
                    '💰 Validasi Pembayaran Denda',
                    "Admin {$adminName} telah memvalidasi pembayaran denda sebesar Rp {$fineAmount} " .
                    "untuk anggota {$loan->user->name} pada buku '{$loan->book->judul}'."
                );
            }

            return redirect()->back()
                ->with('success', "Pembayaran denda sebesar Rp {$fineAmount} berhasil divalidasi.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // METHOD BARU: LIHAT DAFTAR DENDA YANG BELUM DIBAYAR
    // ============================================================
    public function unpaidFines()
    {
        $loans = Loan::where('fine', '>', 0)
                     ->where('fine_status', 'belum_bayar')
                     ->with(['user', 'book'])
                     ->orderBy('created_at', 'desc')
                     ->get();
        
        return view('admin.fines.index', compact('loans'));
    }

    // ============================================================
    // METHOD BARU: LIHAT HISTORI PEMBAYARAN DENDA
    // ============================================================
    public function fineHistory()
    {
        $loans = Loan::where('fine', '>', 0)
                     ->where('fine_status', 'sudah_bayar')
                     ->with(['user', 'book'])
                     ->orderBy('fine_paid_at', 'desc')
                     ->get();
        
        return view('admin.fines.history', compact('loans'));
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

    // ============================================================
    // PERPANJANGAN DENGAN KONFIRMASI ADMIN
    // ============================================================

    // Anggota mengajukan perpanjangan
    public function extend($id)
    {
        $loan = Loan::where('user_id', auth()->id())
                    ->where('id', $id)
                    ->where('status', 'dipinjam')
                    ->first();

        if (!$loan) {
            return redirect()->back()->with('error', 'Peminjaman tidak ditemukan atau tidak bisa diperpanjang');
        }

        if ($loan->extended_count >= 1) {
            return redirect()->back()->with('error', 'Maksimal perpanjangan hanya 1 kali');
        }

        if (now() > $loan->due_date) {
            return redirect()->back()->with('error', 'Buku sudah melewati jatuh tempo');
        }

        // Cek apakah sudah ada pengajuan perpanjangan yang menunggu
        if ($loan->extend_status === 'menunggu') {
            return redirect()->back()->with('error', 'Pengajuan perpanjangan sudah diajukan, menunggu admin');
        }

        $loan->extend_status = 'menunggu';
        $loan->extend_requested_at = now();
        $loan->save();

        // Notifikasi ke admin (user dengan role admin)
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::send(
                $admin->id,
                '📅 Pengajuan Perpanjangan',
                "Anggota {$loan->user->name} mengajukan perpanjangan buku '{$loan->book->judul}'"
            );
        }

        return redirect()->back()->with('success', 'Pengajuan perpanjangan berhasil dikirim, menunggu admin');
    }

    // Admin menyetujui perpanjangan
    public function approveExtend($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $loan = Loan::findOrFail($id);

                if ($loan->extend_status !== 'menunggu') {
                    throw new \Exception('Tidak ada pengajuan perpanjangan');
                }

                // Perpanjang 7 hari
                $loan->due_date = $loan->due_date->addDays(7);
                $loan->extended_count += 1;
                $loan->extend_status = 'disetujui';
                $loan->save();

                Notification::send(
                    $loan->user_id,
                    '✅ Perpanjangan Disetujui',
                    "Perpanjangan buku '{$loan->book->judul}' disetujui oleh admin. Jatuh tempo baru: " . 
                    $loan->due_date->format('d/m/Y')
                );
            });

            return redirect()->back()->with('success', 'Perpanjangan disetujui');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Admin menolak perpanjangan
    public function rejectExtend($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $loan = Loan::findOrFail($id);

                if ($loan->extend_status !== 'menunggu') {
                    throw new \Exception('Tidak ada pengajuan perpanjangan');
                }

                $loan->extend_status = 'ditolak';
                $loan->save();

                Notification::send(
                    $loan->user_id,
                    '❌ Perpanjangan Ditolak',
                    "Perpanjangan buku '{$loan->book->judul}' ditolak oleh admin."
                );
            });

            return redirect()->back()->with('success', 'Perpanjangan ditolak');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}