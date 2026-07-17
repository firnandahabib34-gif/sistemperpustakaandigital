<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Upload background login
     */
    public function uploadBackground(Request $request)
    {
        // Validasi file
        $request->validate([
            'background' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048'
        ]);

        try {
            $file = $request->file('background');
            $path = public_path('backgrounds');
            
            // Buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Simpan file dengan nama tetap
            $file->move($path, 'login-bg.jpg');
            
            return redirect()->back()->with('success', '✅ Background berhasil diupload!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal upload background: ' . $e->getMessage());
        }
    }

    /**
     * Upload logo
     */
    public function uploadLogo(Request $request)
    {
        // Validasi file
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:1024'
        ]);

        try {
            $file = $request->file('logo');
            $path = public_path('logos');
            
            // Buat folder jika belum ada
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Simpan file dengan nama tetap
            $file->move($path, 'logo-polibatam.png');
            
            return redirect()->back()->with('success', '✅ Logo berhasil diupload!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal upload logo: ' . $e->getMessage());
        }
    }
}