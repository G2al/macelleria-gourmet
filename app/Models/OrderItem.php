<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'quantity_type',
    ];

    /**
     * Ogni item appartiene a un ordine.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Ogni item fa riferimento a un prodotto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}