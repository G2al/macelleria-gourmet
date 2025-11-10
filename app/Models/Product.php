<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'photo',
        'category_id',
        'price_per_kg',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
