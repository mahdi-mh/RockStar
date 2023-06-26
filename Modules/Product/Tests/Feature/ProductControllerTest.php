<?php

namespace Modules\Product\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Database\Seeders\ProductSeeder;
use Modules\Product\Models\Product;
use Schema;
use Symfony\Component\Routing\Annotation\Route;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_unauthenticated_can_not_access_products_list(): void
    {
        $response = $this->getJson('/api/products');
        $response->assertUnauthorized();
    }

    /**
     * @return void
     */
    public function test_get_product_list(): void
    {
        $this->seed([
           ProductSeeder::class,
        ]);

        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson('/api/products');

        $response->assertOk();
        $response->assertJsonStructure([
            'per_page',
            'current_page',
            'total',
        ]);
    }
}
