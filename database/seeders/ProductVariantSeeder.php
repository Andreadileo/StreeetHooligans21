<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Controlliamo che ci siano prodotti
        if (Product::count() === 0) {
            $this->command->warn('⚠️ Nessun prodotto trovato. Inserisci prima alcuni prodotti.');
            return;
        }

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL']; // includi tutte le taglie principali

        $this->command->info('Creazione varianti per ' . Product::count() . ' prodotti...');

        foreach (Product::all() as $product) {
            foreach ($sizes as $size) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'size' => $size,
                    ],
                    [
                        'sku' => strtoupper(Str::slug($product->slug ?? $product->name) . '-' . $size),
                        'stock' => rand(1, 8),
                        // lascia price_cents null così usa quello del prodotto
                        'price_cents' => null,
                    ]
                );
            }
        }

        $this->command->info('✅ Varianti create con successo!');
    }
}
