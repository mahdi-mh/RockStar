<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Database\Seeders\ProductSeeder;
use Modules\Product\Models\Product;
use Schema;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_products_table_is_created(): void
    {
        $this->assertTrue(Schema::hasTable('products'));
    }

    /**
     * @return void
     */
    public function test_seeder_creates_products(): void
    {
        $this->seed([
            ProductSeeder::class,
        ]);

        $products = Product::all();

        $this->assertCount(6, $products);
    }
}
