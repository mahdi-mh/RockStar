<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = collect([
            'Latte',
            'Cappuccino',
            'Espresso',
            'Tea',
            'Hot chocolate',
            'Cookie'
        ]);

        return [
            'name' => $products->random()->first(),
            'description' => fake()->sentence,
            'price' => fake()->randomFloat(),
        ];
    }
}
