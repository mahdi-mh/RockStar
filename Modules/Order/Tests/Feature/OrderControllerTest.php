<?php

namespace Modules\Order\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;
use Modules\Product\Database\Seeders\ProductSeeder;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test list orders unauthenticated
     *
     * @return void
     */
    public function test_unauthenticated_can_not_access_orders_list(): void
    {
        $response = $this->getJson('/api/order/list');
        $response->assertUnauthorized();
    }

    /**
     * Test list orders
     *
     * @return void
     */
    public function test_get_orders_list(): void
    {
        $this->seed([
            ProductSeeder::class,
            OrderDatabaseSeeder::class,
        ]);

        $user = User::latest()->first();
        $this->actingAs($user);

        $response = $this->getJson('/api/order/list');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'status',
                    'consume_location',
                    'address',
                    'price',
                    'products' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'details' => [
                                '*' => [
                                    'id',
                                    'name',
                                    'value'
                                ],
                            ]
                        ],
                    ]
                ]
            ]
        ]);
    }
}
