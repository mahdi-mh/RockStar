<?php

namespace Modules\Order\Tests\Feature;

use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;
use Modules\Order\Models\Order;
use Modules\Product\Database\Seeders\ProductSeeder;
use Modules\Product\Models\Product;
use Schema;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test  the orders table is created.
     */
    public function test_orders_table_is_created(): void
    {
        $this->assertTrue(Schema::hasTable('orders'));
    }

    /**
     * Test the seeder creates orders.
     */
    public function test_seeder_creates_orders(): void
    {
        $this->seed([
            OrderDatabaseSeeder::class,
        ]);

        $this->assertTrue(Order::count() > 0);
    }

    /**
     * Test the seeder creates orders with products.
     */
    public function test_seeder_creates_orders_with_products(): void
    {
        $this->seed([
            ProductSeeder::class,
            OrderDatabaseSeeder::class,
        ]);

        $this->assertTrue(DB::table('order_product')->count() > 0);
    }

    /**
     * Test the order has relation to products.
     */
    public function test_product_relation_to_detail(): void
    {
        $this->seed([
            ProductSeeder::class,
            OrderDatabaseSeeder::class,
        ]);

        $order = Order::first();

        $this->assertInstanceOf(Order::class, $order);
        $this->assertInstanceOf(Product::class, $order->products->first());
    }
}
