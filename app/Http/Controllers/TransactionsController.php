<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\PaymentMethods;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    /**
     * Tampilkan semua daftar transaksi (biasanya untuk panel admin/riwayat).
     */
    public function index()
    {
        $transactions = Transactions::with(['product.category', 'paymentMethod', 'user'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Proses pembuatan transaksi baru (Checkout).
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari user
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'target_user_id' => 'required|string|max:100',
            'target_zone_id' => 'nullable|string|max:50',
            'phone_number' => 'required|string|max:20',
        ]);

        try {
            // Gunakan Database Transaction agar aman dari inkonsistensi data
            return DB::transaction(function () use ($request) {
                // Ambil data produk & metode pembayaran menggunakan model bentuk jamak
                $product = Products::findOrFail($request->product_id);
                $paymentMethod = PaymentMethods::findOrFail($request->payment_method_id);

                // Hitung total amount (harga produk + biaya admin metode pembayaran)
                $totalAmount = $product->price + $paymentMethod->admin_fee;

                // Generate nomor invoice unik (Contoh: INV-20260909-ABC123)
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

                // 2. Simpan transaksi ke database
                $transaction = Transactions::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => auth()->id(), // Nullable jika menggunakan guest checkout
                    'product_id' => $product->id,
                    'payment_method_id' => $paymentMethod->id,
                    'target_user_id' => $request->target_user_id,
                    'target_zone_id' => $request->target_zone_id,
                    'phone_number' => $request->phone_number,
                    'amount' => $totalAmount,
                    'payment_status' => 'pending',
                    'processing_status' => 'processing',
                ]);

                // TODO: Di sini Anda bisa menambahkan integrasi API Payment Gateway 
                // untuk mendapatkan URL pembayaran atau reference code.

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil dibuat. Silakan lakukan pembayaran.',
                    'data' => $transaction->load(['product', 'paymentMethod'])
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses transaksi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan detail transaksi berdasarkan nomor invoice.
     */
    public function show($invoiceNumber)
    {
        $transaction = Transactions::with(['product.category', 'paymentMethod', 'user'])
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    /**
     * Update status pembayaran atau status proses (untuk keperluan admin / webhook callback).
     */
    public function updateStatus(Request $request, $id)
    {
        $transaction = Transactions::findOrFail($id);

        $request->validate([
            'payment_status' => 'nullable|in:pending,paid,failed,expired',
            'processing_status' => 'nullable|in:processing,success,failed',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $transaction->update($request->only([
            'payment_status',
            'processing_status',
            'payment_reference'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Status transaksi berhasil diperbarui',
            'data' => $transaction
        ]);
    }
}