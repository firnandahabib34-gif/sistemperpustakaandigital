<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->get(); // ambil juga relasi kategori
        return view('admin.books.index', compact('books'));
    }

public function store(Request $request)
{
    $request->validate([
        'judul' => 'required|string|max:255',
        'penulis' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'stok' => 'required|integer|min:0',
        'penerbit' => 'nullable|string|max:255',
        'tahun' => 'nullable|digits:4',
        'isbn' => 'nullable|string|max:20',
        'lokasi_rak' => 'nullable|string|max:50',
        'deskripsi' => 'nullable|string',
        'jumlah_halaman' => 'nullable|integer|min:1',
        'sampul' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $data = $request->all();
    
    // Upload sampul jika ada
    if ($request->hasFile('sampul')) {
        $file = $request->file('sampul');
        $namaFile = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/sampul'), $namaFile);
        $data['sampul'] = 'uploads/sampul/' . $namaFile;
    }

    $book = Book::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Buku berhasil ditambahkan'
    ]);
}

    public function edit($id)
{
    $book = Book::with('category')->findOrFail($id);
    return response()->json($book);
}

    public function update(Request $request, $id)
{
    $book = Book::findOrFail($id);

    $request->validate([
        'judul' => 'required|string|max:255',
        'penulis' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'stok' => 'required|integer|min:0',
        'penerbit' => 'nullable|string|max:255',
        'tahun' => 'nullable|digits:4',
        'isbn' => 'nullable|string|max:20',
        'lokasi_rak' => 'nullable|string|max:50',
        'deskripsi' => 'nullable|string',
        'jumlah_halaman' => 'nullable|integer|min:1',
        'sampul' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $data = $request->except(['_token', '_method']);

    // Upload sampul jika ada
    if ($request->hasFile('sampul')) {
        // Hapus sampul lama jika ada
        if ($book->sampul && file_exists(public_path($book->sampul))) {
            unlink(public_path($book->sampul));
        }
        
        $file = $request->file('sampul');
        $namaFile = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/sampul'), $namaFile);
        $data['sampul'] = 'uploads/sampul/' . $namaFile;
    }

    $book->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Buku berhasil diupdate'
    ]);
}

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['success' => true, 'message' => 'Buku berhasil dihapus']);
    }
}