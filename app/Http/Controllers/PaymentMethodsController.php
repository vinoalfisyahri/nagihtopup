<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethods;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    /**
     * Tampilkan semua metode pembayaran yang aktif.
     */
    public function index()
    {
        $paymentMethods = PaymentMethods::where('status', 'active')->get();
        return response()->json([
            'success' => true, 
            'data' => $paymentMethods
        ]);
    }

    /**
     * Simpan metode pembayaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:payment_methods,code',
            'name' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'admin_fee' => 'required|numeric|min:0',
            'status' => 'in:active,inactive',
        ]);

        $paymentMethod = PaymentMethods::create([
            'code' => $request->code,
            'name' => $request->name,
            'type' => $request->type,
            'admin_fee' => $request->admin_fee ?? 0,
            'status' => $request->status ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil ditambahkan',
            'data' => $paymentMethod
        ], 201);
    }

    /**
     * Tampilkan detail satu metode pembayaran berdasarkan ID.
     */
    public function show($id)
    {
        $paymentMethod = PaymentMethods::findOrFail($id);

        return response()->json([
            'success' => true, 
            'data' => $paymentMethod
        ]);
    }

    /**
     * Update metode pembayaran.
     */
    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethods::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $id,
            'name' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'admin_fee' => 'required|numeric|min:0',
            'status' => 'in:active,inactive',
        ]);

        $paymentMethod->update([
            'code' => $request->code,
            'name' => $request->name,
            'type' => $request->type,
            'admin_fee' => $request->admin_fee,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil diperbarui',
            'data' => $paymentMethod
        ]);
    }

    /**
     * Hapus metode pembayaran.
     */
    public function destroy($id)
    {
        $paymentMethod = PaymentMethods::findOrFail($id);
        $paymentMethod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil dihapus'
        ]);
    }
}