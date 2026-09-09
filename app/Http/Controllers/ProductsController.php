<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Tampilkan semua produk yang tersedia (opsional filter berdasarkan category_id).
     */
    public function index(Request $request)
    {
        $query = Products::where('status', 'available')->with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'provider_code' => 'required|string|max:50',
            'status' => 'in:available,empty',
        ]);

        $product = Products::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'provider_code' => $request->provider_code,
            'status' => $request->status ?? 'available',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    /**
     * Tampilkan detail satu produk berdasarkan ID.
     */
    public function show($id)
    {
        $product = Products::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update data produk.
     */
    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'provider_code' => 'required|string|max:50',
            'status' => 'in:available,empty',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'provider_code' => $request->provider_code,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);
    }

    /**
     * Hapus produk.
     */
    public function destroy($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}