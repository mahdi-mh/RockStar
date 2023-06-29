<?php

namespace Modules\Order\Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;

class OrderSyncWithProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        foreach (Order::all() as $order) {
            $products = Product::with('details')
                ->inRandomOrder()->limit(random_int(1,2))->get();

            foreach ($products as $product) {
                $details = [];

                foreach ($product->details as $detail) {
                    $details[] = [
                        'id' => $detail->id,
                        'name' => $detail->name,
                        'value' => $detail->options->random(),
                    ];
                }

                $order->products()->attach($product, [
                    'details' => json_encode($details, JSON_THROW_ON_ERROR),
                ]);
            }
        }
    }
}
