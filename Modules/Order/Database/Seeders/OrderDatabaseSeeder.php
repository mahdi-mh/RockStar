<?php

namespace Modules\Order\Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Modules\Order\Models\Order;

class OrderDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        User::factory(10)
            ->has(Order::factory()->count(random_int(1,5)))
            ->create();

        $this->call([
            OrderSyncWithProductSeeder::class,
        ]);
    }
}
