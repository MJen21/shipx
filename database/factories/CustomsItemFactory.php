<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomsItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => 'Books',
            'quantity' => $this->faker->randomDigit(),
            'quantity_unit' => 'PCS',
            'net_weight' => $this->faker->randomFloat(3, 5, 10),
            'gross_weight' => $this->faker->randomFloat(3, 5, 10),
            'weight_unit' => 'kg',
            'unit_value' => $this->faker->randomFloat(2, 5, 10),
            'tariff_number' => '',
            'origin_country' => 'TW',
        ];
    }
}
