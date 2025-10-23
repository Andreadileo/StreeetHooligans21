<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // 1) assicurati che product_id abbia un indice “semplice”
            // (serve per la FK verso products)
            $table->index('product_id', 'product_variants_product_id_index');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            // 2) ora puoi droppare il vecchio unique a 3 colonne
            //    (nome generato da Laravel)
            try {
                $table->dropUnique('product_variants_product_id_size_color_unique');
            } catch (\Throwable $e) {
                // ignora se non esiste
            }

            // 3) crea il nuovo unique su (product_id, size)
            $table->unique(['product_id','size'], 'product_variants_product_id_size_unique');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // ripristina la situazione precedente
            try {
                $table->dropUnique('product_variants_product_id_size_unique');
            } catch (\Throwable $e) {}

            $table->unique(
                ['product_id','size','color'],
                'product_variants_product_id_size_color_unique'
            );

            // opzionale: rimuovi l’indice semplice se non lo vuoi più
            try {
                $table->dropIndex('product_variants_product_id_index');
            } catch (\Throwable $e) {}
        });
    }
};
