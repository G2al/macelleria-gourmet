<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Cliente che effettua l’ordine
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Prezzo totale dell'intero ordine (somma degli items)
            $table->decimal('total_price', 10, 2)->default(0);

            // Dettagli di ritiro
            $table->date('pickup_date')->comment('Data ritiro');
            $table->time('pickup_time')->comment('Ora ritiro');

            // Stato dell’ordine
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                ->default('pending');

            // Note facoltative del cliente
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
