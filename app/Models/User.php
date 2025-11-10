<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Filament
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * Consenti l’accesso al pannello Filament solo agli admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Attributi assegnabili in massa.
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * Attributi nascosti in serializzazione.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast degli attributi.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
