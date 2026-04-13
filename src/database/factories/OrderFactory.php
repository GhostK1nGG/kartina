<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'session_id' => fake()->uuid(),
            'customer_name' => fake()->name(),
            'contact' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'comment' => fake()->sentence(),
            'cart_snapshot' => [
                'items' => [
                    [
                        'painting_id' => 1,
                        'title' => 'Test painting',
                        'quantity' => 1,
                        'price_rub' => 10000,
                        'price_usd' => 100,
                        'subtotal_rub' => 10000,
                        'subtotal_usd' => 100,
                    ],
                ],
                'total_rub' => 10000,
                'total_usd' => 100,
                'total_quantity' => 1,
                'is_empty' => false,
            ],
            'total_rub' => 10000,
            'total_usd' => 100,
            'status' => 'new',
        ];
    }
}
