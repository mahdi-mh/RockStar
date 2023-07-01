<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Database\Seeders\ProductDetailSeeder;
use Modules\Product\Database\Seeders\ProductSeeder;
use Modules\Product\Database\Seeders\ProductSyncWithDetailSeeder;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductDetail;
use Schema;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test  the products table is created.
     */
    public function test_products_table_is_created(): void
    {
        $this->assertTrue(Schema::hasTable('products'));
    }

    /**
     * Test the seeder creates products.
     */
    public function test_seeder_creates_products(): void
    {
        $this->seed([
            ProductSeeder::class,
        ]);

        $products = Product::all();

        $this->assertCount(6, $products);
    }

    /**
     * Test the product relation to detail.
     */
    public function test_product_relation_to_detail(): void
    {
        $this->seed([
            ProductSeeder::class,
            ProductDetailSeeder::class,
            ProductSyncWithDetailSeeder::class,
        ]);

        $product = Product::first();
        $this->assertInstanceOf(Product::class, $product);
        $this->assertInstanceOf(ProductDetail::class, $product->details->first());
    }
}
