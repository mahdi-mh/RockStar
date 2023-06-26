<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\ProductDetail;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = collect([
            'milk' => ['skim', 'semi', 'whole'],
            'shots' => ['single', 'double', 'triple'],
            'size' => ['small', 'medium', 'large'],
            'kind' => ['chocolate', 'chip', 'ginger'],
        ]);

        foreach ($products as $name => $options) {
            ProductDetail::create([
                'name' => $name,
                'options' => (object) $options,
            ]);
        }
    }
}
