<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([['name'=>'T-Shirt','slug'=>'t-shirt'],['name'=>'Hoodie','slug'=>'hoodie'],['name'=>'Accessories','slug'=>'accessories']] as $c) {
            Category::firstOrCreate(['slug'=>$c['slug']], $c);
        }
    }
}
