<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    // Menampilkan daftar anggota
    public function index()
    {
        $anggota = User::where('role', 'anggota')->get();
        return view('admin.anggota.index', compact('anggota'));
    }

    // Menyimpan anggota baru
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:15|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'prodi' => $request->prodi,
            'role' => 'anggota',
            'status' => 'aktif'
        ]);

        return response()->json(['success' => true, 'message' => 'Anggota berhasil ditambahkan']);
    }

    // Ambil data anggota untuk edit
    public function edit($id)
    {
        $anggota = User::findOrFail($id);
        return response()->json($anggota);
    }

    // Update anggota
    public function update(Request $request, $id)
    {
        $anggota = User::findOrFail($id);

        $request->validate([
            'nim' => 'required|string|max:15|unique:users,nim,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $data = [
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'prodi' => $request->prodi,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $anggota->update($data);

        return response()->json(['success' => true, 'message' => 'Anggota berhasil diupdate']);
    }

    // Hapus anggota
    public function destroy($id)
    {
        $anggota = User::findOrFail($id);
        $anggota->delete();
        return response()->json(['success' => true, 'message' => 'Anggota berhasil dihapus']);
    }

    // Toggle status (aktif/nonaktif)
    public function toggleStatus($id)
    {
        $anggota = User::findOrFail($id);
        $anggota->status = $anggota->status === 'aktif' ? 'nonaktif' : 'aktif';
        $anggota->save();
        return response()->json(['success' => true, 'message' => 'Status berhasil diubah']);
    }
}