<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = collect([
            'Latte',
            'Cappuccino',
            'Espresso',
            'Tea',
            'Hot chocolate',
            'Cookie'
        ]);

        foreach ($products as $product) {
            Product::factory()
                ->create([
                    'name' => $product
                ]);
        }
    }
}
