<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;
use Modules\Product\Database\Seeders\ProductDetailSeeder;
use Modules\Product\Database\Seeders\ProductSeeder;
use Modules\Product\Database\Seeders\ProductSyncWithDetailSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            ProductDetailSeeder::class,
            ProductSyncWithDetailSeeder::class,
            OrderDatabaseSeeder::class,
        ]);
    }
}
