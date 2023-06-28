<?php

namespace Modules\Order\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;
use Modules\Order\Enums\OrderConsumeLocation;
use Modules\Product\Database\Seeders\ProductSeeder;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Test store order unauthenticated
     */
    public function test_store_order_unauthenticated(): void
    {
        $response = $this->postJson('/api/order/store');
        $response->assertUnauthorized();
    }

    /**
     * Test store order invalid data
     */
    public function test_store_order_invalid_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/order/store');
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'errors' => ['consume_location']
        ]);
    }

    /**
     * Test order store in shop success
     */
    public function test_store_order_success_in_shop(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/order/store', [
            'consume_location' => OrderConsumeLocation::IN_SHOP->value,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'id',
            'consume_location'
        ]);
    }

    /**
     * Test store order already ordering exist
     */
    public function test_store_order_already_ordering_exist(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/order/store', [
            'consume_location' => OrderConsumeLocation::IN_SHOP->value,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response = $this->postJson('/api/order/store', [
            'consume_location' => OrderConsumeLocation::IN_SHOP->value,
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
        $response->assertJson([
            'message' => 'This user already have active order',
        ]);
    }

    /**
     * Test store order take away invalid data
     */
    public function test_store_order_take_away_invalid_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/order/store', [
            'consume_location' => OrderConsumeLocation::TAKE_AWAY->value,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'errors' => ['address']
        ]);
    }

    /**
     * Test store order take away success
     */
    public function test_store_order_success_take_away(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/order/store', [
            'consume_location' => OrderConsumeLocation::TAKE_AWAY->value,
            'address' => fake()->address,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'id',
            'consume_location',
            'address',
        ]);
    }
}
