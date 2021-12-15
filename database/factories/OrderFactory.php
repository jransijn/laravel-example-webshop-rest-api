<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->uuid(),
            'total_amount' => $this->faker->randomFloat(5, 0.0, 400.0),
            'status' => $this->faker->randomElement([ 'pending', 'paid', 'shipped' ]),
        ];
    }
}
