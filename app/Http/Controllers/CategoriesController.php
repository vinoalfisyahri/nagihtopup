<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    /**
     * Tampilkan semua kategori aktif beserta produknya.
     */
    public function index()
    {
        $categories = Categories::where('status', 'active')->with('products')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'publisher' => 'nullable|string|max:100',
            'target_field_type' => 'required|string|max:50',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'in:active,inactive',
        ]);

        $path = null;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $category = Categories::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'publisher' => $request->publisher,
            'target_field_type' => $request->target_field_type,
            'thumbnail' => $path,
            'status' => $request->status ?? 'active',
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Kategori berhasil ditambahkan', 
            'data' => $category
        ], 201);
    }

    /**
     * Tampilkan detail kategori berdasarkan ID.
     */
    public function show($id)
    {
        $category = Categories::with('products')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $category]);
    }

    /**
     * Update data kategori.
     */
    public function update(Request $request, $id)
    {
        $category = Categories::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'publisher' => 'nullable|string|max:100',
            'target_field_type' => 'required|string|max:50',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'in:active,inactive',
        ]);

        $path = $category->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'publisher' => $request->publisher,
            'target_field_type' => $request->target_field_type,
            'thumbnail' => $path,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Kategori berhasil diperbarui', 
            'data' => $category
        ]);
    }

    /**
     * Hapus kategori.
     */
    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        
        if ($category->thumbnail) {
            Storage::disk('public')->delete($category->thumbnail);
        }
        
        $category->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}