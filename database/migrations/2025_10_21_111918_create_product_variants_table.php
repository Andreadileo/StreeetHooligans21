<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('size')->nullable();   // es. S, M, L, 42
            $table->string('color')->nullable();  // se prendi dal prodotto, resta coerente
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('price_cents')->nullable(); // di default useremo price del prodotto
            $table->timestamps();

            $table->unique(['product_id','size','color']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_variants');
    }
};
