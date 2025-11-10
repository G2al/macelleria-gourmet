<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Crea utente admin solo se non esiste già
        if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'surname' => 'Gourmet',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'phone' => '0000000000',
                'role' => 'admin',
            ]);
        }
    }
}
