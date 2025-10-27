<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Nike Air Zoom Pegasus 41',
                'description' => 'Scarpa da running versatile con ammortizzazione ZoomX e mesh traspirante.',
                'price' => 139.99,
                'image' => 'https://static.nike.com/a/images/t_default/7f19e9a5-1e5a-44cf-b5c0-27f4a134af7c/air-zoom-pegasus-41-scarpe-running-uomo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Dunk Low Retro',
                'description' => 'Stile iconico anni 80 con tomaia in pelle e comfort moderno.',
                'price' => 119.99,
                'image' => 'https://static.nike.com/a/images/t_default/76178e0d-6424-4b88-8fd1-77705c685b3a/dunk-low-retro-scarpe.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Tech Fleece Hoodie',
                'description' => 'Felpa con cappuccio in tessuto tecnico leggero e caldo, perfetta per lo sport o il tempo libero.',
                'price' => 99.99,
                'image' => 'https://static.nike.com/a/images/t_default/3b7f52f1-5741-4785-b671-96e2efb3a8cb/sportswear-tech-fleece-felpa-con-cappuccio.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Design audace con unità Air 270 visibile e comfort eccezionale tutto il giorno.',
                'price' => 159.99,
                'image' => 'https://static.nike.com/a/images/t_default/26eaa1b0-3c67-42a8-a343-fc2a4c9316ee/air-max-270-scarpe.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Sportswear Club Pants',
                'description' => 'Pantaloni in cotone morbido e vestibilità regolare per comfort quotidiano.',
                'price' => 49.99,
                'image' => 'https://static.nike.com/a/images/t_default/bddda49b-c57d-49a2-bb14-41f8c7d7f226/sportswear-club-fleece-pantaloni.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Air Force 1 ’07',
                'description' => 'Un’icona reinventata. Pelle premium e suola resistente per un look senza tempo.',
                'price' => 129.99,
                'image' => 'https://static.nike.com/a/images/t_default/8cbfcf79-337a-4939-8c45-7853729f568f/air-force-1-07-scarpe.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Pro Dri-FIT Tee',
                'description' => 'T-shirt da allenamento traspirante, con tecnologia Dri-FIT per prestazioni elevate.',
                'price' => 34.99,
                'image' => 'https://static.nike.com/a/images/t_default/08b183c3-f51e-45a7-bb3d-fb3ce53606b7/pro-dri-fit-t-shirt.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Metcon 9',
                'description' => 'Scarpe da training con stabilità superiore e design rinforzato per il sollevamento pesi.',
                'price' => 139.99,
                'image' => 'https://static.nike.com/a/images/t_default/9f534122-1e90-4f09-a91d-8fd3247ab02c/metcon-9-scarpe-da-training.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Air Jordan 1 Mid',
                'description' => 'Classico intramontabile con tomaia in pelle e suola Air per il massimo comfort.',
                'price' => 149.99,
                'image' => 'https://static.nike.com/a/images/t_default/4cb6db62-2f2a-4f27-b91e-67ec30cf26a2/air-jordan-1-mid-scarpe.png',
                'is_active' => true,
            ],
            [
                'name' => 'Nike Essential Backpack',
                'description' => 'Zaino pratico e resistente, con scomparti multipli e logo iconico.',
                'price' => 54.99,
                'image' => 'https://static.nike.com/a/images/t_default/16ecad24-960c-4c9a-9a7a-8b30b207d3cf/essential-backpack.png',
                'is_active' => true,
            ],
        ];

        foreach ($products as $data) {
            $data['image_ratio'] = $data['image_ratio'] ?? 'standard';
            Product::create($data);
        }
    }
}
