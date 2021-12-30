<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomsDeclarationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'invoice_date' => date('Y-m-d'),
            'invoice_number' => '',
            'incoterm' => 'DDU',
            'currency' => 'USD',
        ];
    }
}
