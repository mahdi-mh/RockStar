<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductDetail;

class ProductSyncWithDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $products = collect([
            'Latte' => [
                'milk',
                'shots',
                'size',
            ],
            'Cappuccino' => [
                'size',
            ],
            'Espresso' => [
                'shots',
                'size',
            ],
            'Tea' => [
                'size',
            ],
            'Hot chocolate' => [
                'milk',
                'size',
            ],
            'Cookie' => [
                'size',
                'kind',
            ],
        ]);

        foreach ($products as $name => $details) {
            $details = ProductDetail::whereIn('name', $details)->get();
            $product = Product::whereName($name)->first();
            $product->details()->sync($details);
        }
    }
}
