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

class ProductDetailModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if product details table is created
     */
    public function test_products_details_table_is_created(): void
    {
        $this->assertTrue(Schema::hasTable('product_details'));
    }

    /**
     * Test if seeder creates product details
     */
    public function test_seeder_creates_product_details(): void
    {
        $this->seed([
            ProductDetailSeeder::class,
        ]);

        $productsDetails = ProductDetail::all();

        $this->assertCount(4, $productsDetails);
    }

    public function test_product_detail_has_product()
    {
        $this->seed([
            ProductSeeder::class,
            ProductDetailSeeder::class,
            ProductSyncWithDetailSeeder::class,
        ]);

        $detail = ProductDetail::first();

        $this->assertNotNull($detail->product);
        $this->assertInstanceOf(Product::class, $detail->product->first());
    }
}
