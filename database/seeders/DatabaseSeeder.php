<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([CategorySeeder::class, ProductSeeder::class]);
        $this->call(ProductVariantSeeder::class);

        User::factory()->create([
            'name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@shop.test',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }
}
