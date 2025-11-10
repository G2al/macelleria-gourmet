<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'opening_time',
        'closing_time',
        'is_active',
    ];

    /**
     * Restituisce il nome del giorno (es. "Lunedì", "Martedì"...)
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            0 => 'Lunedì',
            1 => 'Martedì',
            2 => 'Mercoledì',
            3 => 'Giovedì',
            4 => 'Venerdì',
            5 => 'Sabato',
            6 => 'Domenica',
        ];

        return $days[$this->day_of_week] ?? 'Sconosciuto';
    }
}
