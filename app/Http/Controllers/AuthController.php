<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
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
            'nim' => 'required',
            'password' => 'required',
        ]);

        // Cari user berdasarkan NIM
        $user = User::where('nim', $request->nim)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            
            if ($user->role === 'admin') {
                return redirect()->intended('/dashboard-admin');
            } else {
                return redirect()->intended('/dashboard-anggota');
            }
        }

        return back()->withErrors([
            'nim' => 'NIM atau password salah.',
        ])->withInput();
    }

    // Tampilkan halaman register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

 // Proses register (Redirect ke halaman login)
public function register(Request $request)
{
    // Validasi input
    $request->validate([
        'nim' => 'required|string|max:15|unique:users',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'prodi' => 'nullable|string|max:50',
        'phone' => 'nullable|string|max:15',
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

    // Redirect ke halaman login dengan pesan sukses (TIDAK LANGSUNG LOGIN)
    return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
}

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}