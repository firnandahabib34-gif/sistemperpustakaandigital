<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.kategori.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:categories',
            'deskripsi' => 'nullable|string'
        ]);

        Category::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan']);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:categories,nama,' . $id,
            'deskripsi' => 'nullable|string'
        ]);

        $category->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil diupdate']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
       
        if ($category->books()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Kategori masih digunakan di buku, tidak bisa dihapus'], 400);
        }
        
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus']);
    }
}