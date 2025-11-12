<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) PRODUCTS: aggiungi purchase_type
        Schema::table('products', function (Blueprint $table) {
            $table->enum('purchase_type', ['weight', 'unit', 'package'])
                ->default('weight')
                ->after('category_id')
                ->comment('weight=kg, unit=pezzi, package=confezioni');
        });

        // 2) PRODUCTS: rimuovi price_per_kg
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price_per_kg');
        });

        // 3) ORDER_ITEMS: rimuovi campi di prezzo
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['weight', 'price_per_kg', 'total_price']);
        });

        // 4) ORDER_ITEMS: aggiungi quantity + quantity_type
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)
                ->after('product_id')
                ->comment('Valore numerico: kg, pezzi o confezioni');
            
            $table->enum('quantity_type', ['weight', 'unit', 'package'])
                ->after('quantity')
                ->default('weight')
                ->comment('Tipo di quantità ordinata');
        });

        // 5) ORDERS: rimuovi total_price
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('purchase_type');
            $table->decimal('price_per_kg', 8, 2)->after('category_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'quantity_type']);
            $table->decimal('weight', 8, 2)->after('product_id');
            $table->decimal('price_per_kg', 8, 2);
            $table->decimal('total_price', 10, 2);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->default(0);
        });
    }
};