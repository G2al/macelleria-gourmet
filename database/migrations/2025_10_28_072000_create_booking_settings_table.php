<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_settings', function (Blueprint $table) {
            $table->id();
            $table->time('booking_start_time')->default('08:00'); // inizio fascia prenotazione
            $table->time('booking_end_time')->default('21:00');   // fine fascia prenotazione
            $table->unsignedTinyInteger('min_days_advance')->default(1); // giorni minimi di anticipo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_settings');
    }
};
