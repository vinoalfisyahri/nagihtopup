<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transactions extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'user_id',
        'product_id',
        'payment_method_id',
        'target_user_id',
        'target_zone_id',
        'phone_number',
        'amount',
        'payment_status',
        'processing_status',
        'payment_reference',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the product associated with the transaction.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }

    /**
     * Get the payment method used for the transaction.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethods::class);
    }

    /**
     * Get the user that owns the transaction (if registered).
     */
    public function user(): BelongsTo
    {
        // Sesuaikan dengan namespace Model User Anda jika menggunakan relasi ini
        return $this->belongsTo(User::class);
    }
}