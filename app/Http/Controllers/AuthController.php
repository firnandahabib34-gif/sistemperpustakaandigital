<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller  // Perbaikan: Nama class harus lengkap
{
    // Tampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:admin,anggota',
        ]);

        // Cari user berdasarkan NIM
        $user = User::where('nim', $request->nim)->first();

        // Perbaikan: Cek status user juga
        if ($user && Hash::check($request->password, $user->password)) {

    // Cek role
    if ($user->role !== $request->role) {
        return back()->withErrors([
            'role' => 'Peran yang dipilih tidak sesuai.',
        ])->withInput();
    }

    // Cek apakah user aktif
            // Cek apakah user aktif
            if ($user->status !== 'aktif') {
                return back()->withErrors([
                    'nim' => 'Akun Anda tidak aktif. Silakan hubungi admin.',
                ])->withInput();
            }

            Auth::login($user);
            $request->session()->regenerate(); // Tambahan: regenerate session untuk keamanan
            
            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->intended('/dashboard-admin');
            } else {
                return redirect()->intended('/dashboard-anggota');
            }
        }

        return back()->withErrors([
            'nim' => 'NIM atau password salah.',
        ])->onlyInput('nim'); // Perbaikan: hanya retain input nim, bukan password
    }

    // Tampilkan halaman register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'nim' => 'required|string|max:15|unique:users,nim',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // Perbaikan: minimal 8 karakter (lebih aman)
            'prodi' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15|regex:/^[0-9]+$/', // Tambahan: validasi nomor telepon
        ], [
            // Custom error messages (opsional)
            'nim.unique' => 'NIM sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        // Buat user baru
        User::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'prodi' => $request->prodi,
            'phone' => $request->phone,
            'role' => 'anggota',
            'status' => 'aktif'
        ]);

        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }
}