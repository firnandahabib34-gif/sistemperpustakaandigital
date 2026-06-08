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
        ]);

        $book = Book::create([
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'category_id' => $request->category_id,
            'stok' => $request->stok,
            'penerbit' => $request->penerbit,
            'tahun' => $request->tahun,
        ]);

        return response()->json(['success' => true, 'message' => 'Buku berhasil ditambahkan', 'data' => $book]);
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
        ]);

        $book->update([
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'category_id' => $request->category_id,
            'stok' => $request->stok,
            'penerbit' => $request->penerbit,
            'tahun' => $request->tahun,
        ]);

        return response()->json(['success' => true, 'message' => 'Buku berhasil diupdate']);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['success' => true, 'message' => 'Buku berhasil dihapus']);
    }
}