<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup_date',
        'pickup_time',
        'status',
        'notes',
    ];

    /**
     * Relazione: un ordine appartiene a un utente.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione: un ordine ha molti item (prodotti collegati).
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}