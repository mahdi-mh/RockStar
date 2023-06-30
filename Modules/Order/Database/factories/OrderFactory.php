<?php

namespace Modules\Order\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\Enums\OrderConsumeLocation;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker
                ->randomElement(collect(OrderStatus::cases())->pluck('value')->toArray()),
            'consume_location' => $this->faker
                ->randomElement(collect(OrderConsumeLocation::cases())->pluck('value')->toArray()),
            'address' => $this->faker->address,
            'total_price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}

