<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;

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
