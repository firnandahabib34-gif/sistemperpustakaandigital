<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LoanFineSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. CEK KONEKSI DATABASE
        // ============================================================
        try {
            DB::connection()->getPdo();
            $this->command->info('✅ Koneksi database berhasil');
        } catch (\Exception $e) {
            $this->command->error('❌ Koneksi database gagal: ' . $e->getMessage());
            return;
        }

        // ============================================================
        // 2. AMBIL ADMIN
        // ============================================================
        $admin = User::where('email', 'admin@library.com')->first();
        
        if (!$admin) {
            $this->command->error('❌ Admin tidak ditemukan!');
            $this->command->info('📝 Silakan jalankan UserSeeder dulu:');
            $this->command->info('   php artisan db:seed --class=UserSeeder');
            return;
        }
        $this->command->info('✅ Admin ditemukan: ' . $admin->name);

        // ============================================================
        // 3. AMBIL BUKU YANG SUDAH ADA
        // ============================================================
        $book = Book::first();
        
        if (!$book) {
            $this->command->error('❌ Tidak ada buku di database!');
            $this->command->info('📝 Silakan jalankan BookSeeder dulu:');
            $this->command->info('   php artisan db:seed --class=BookSeeder');
            
            // Tampilkan semua tabel yang ada
            $tables = DB::select('SHOW TABLES');
            $this->command->info('📋 Tabel yang ada:');
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                $this->command->info("   - $tableName");
            }
            return;
        }
        $this->command->info('✅ Buku ditemukan: ' . $book->judul . ' (ID: ' . $book->id . ')');

        // ============================================================
        // 4. BUAT ATAU AMBIL AKUN SITI
        // ============================================================
        $user = User::where('email', 'siti@student.com')->first();
        
        if (!$user) {
            try {
                $user = User::create([
                    'nim' => '3312511013',
                    'name' => 'Siti Rahmawati',
                    'email' => 'siti@student.com',
                    'password' => Hash::make('password'),
                    'prodi' => 'Teknik Informatika',
                    'phone' => '081234567891',
                    'role' => 'anggota',
                    'status' => 'aktif'
                ]);
                $this->command->info('✅ Akun Siti Rahmawati berhasil dibuat');
            } catch (\Exception $e) {
                $this->command->error('❌ Gagal membuat akun Siti: ' . $e->getMessage());
                
                // Tampilkan struktur tabel users
                $columns = DB::getSchemaBuilder()->getColumnListing('users');
                $this->command->info('📋 Kolom di tabel users:');
                foreach ($columns as $col) {
                    $this->command->info("   - $col");
                }
                return;
            }
        } else {
            $this->command->info('✅ Akun Siti Rahmawati sudah ada (ID: ' . $user->id . ')');
        }

        // ============================================================
        // 5. CEK APAKAH SUDAH ADA LOAN
        // ============================================================
        $existingLoan = Loan::where('user_id', $user->id)
                            ->where('book_id', $book->id)
                            ->where('status', 'dikembalikan')
                            ->first();

        if ($existingLoan) {
            $this->command->info('⏭️  Data loan untuk Siti sudah ada, dilewati');
            $this->command->info('   ID Loan: ' . $existingLoan->id);
            $this->command->info('   Status: ' . $existingLoan->status);
            $this->command->info('   Denda: Rp ' . number_format($existingLoan->fine, 0, ',', '.'));
        } else {
            // ============================================================
            // 6. BUAT DATA LOAN DENGAN DENDA
            // ============================================================
            try {
                Loan::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'admin_id' => $admin->id,
                    'borrow_date' => Carbon::now()->subDays(10),
                    'due_date' => Carbon::now()->subDays(3),
                    'return_date' => Carbon::now()->subDays(1),
                    'status' => 'dikembalikan',
                    'anggota_confirmed' => 1,
                    'fine' => 6000,
                    'fine_status' => 'belum_bayar',
                    'fine_paid_at' => null,
                    'fine_paid_by' => null,
                    'extended_count' => 0,
                    'extend_status' => null,
                    'extend_requested_at' => null,
                    'created_at' => Carbon::now()->subDays(10),
                    'updated_at' => Carbon::now()->subDays(1),
                ]);
                $this->command->info('✅ Data loan berhasil dibuat!');
                $this->command->info('   👤 Siti Rahmawati');
                $this->command->info('   📚 ' . $book->judul);
                $this->command->info('   💰 Denda: Rp 6.000');
                $this->command->info('   🔴 Status: BELUM BAYAR');
            } catch (\Exception $e) {
                $this->command->error('❌ Gagal membuat data loan: ' . $e->getMessage());
                
                // Tampilkan struktur tabel loans
                $columns = DB::getSchemaBuilder()->getColumnListing('loans');
                $this->command->info('📋 Kolom di tabel loans:');
                foreach ($columns as $col) {
                    $this->command->info("   - $col");
                }
                return;
            }
        }

        // ============================================================
        // 7. TAMPILKAN INFORMASI AKHIR
        // ============================================================
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('✅ SEEDER LOAN FINE SELESAI!');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📝 AKUN UNTUK TESTING:');
        $this->command->info('   Email    : siti@student.com');
        $this->command->info('   Password : password');
        $this->command->info('   📌 Status : 🔴 Denda Rp 6.000 (BELUM BAYAR)');
        $this->command->info('');
        $this->command->info('🔑 ADMIN:');
        $this->command->info('   Email    : admin@library.com');
        $this->command->info('   Password : admin1');
        $this->command->info('═══════════════════════════════════════════════');
    }
}