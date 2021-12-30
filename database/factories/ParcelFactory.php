<?php

namespace Database\Factories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParcelFactory extends Factory
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
            'shipment_id' => Shipment::factory(),
            'weight' => $this->faker->randomFloat(3, 5, 10),
            'weight_unit' => 'kg',
            'length' => $this->faker->randomFloat(3, 5, 10),
            'width' => $this->faker->randomFloat(3, 5, 10),
            'height' => $this->faker->randomFloat(3, 5, 10),
            'dimension_unit' => 'cm'
        ];
    }
}
