<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::factory(),
            'date' => date('Y-m-d'),
            'type' => 'Non-Doc',
            'purpose' => 'Commercial',
            'contents' => 'Books',
            'service' => 'dhl_express_wpx'
        ];
    }
}
