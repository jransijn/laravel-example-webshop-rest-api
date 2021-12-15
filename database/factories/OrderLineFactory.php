<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => Order::factory()->create()->id,
            'barcode' => $this->faker->uuid(),
            'quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
