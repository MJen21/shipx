<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Mike Jen',
            'company' => '',
            'street1' => 'No. 20, Aly. 1, Ln. 135',
            'street2' => 'Yuying St., Shulin Dist.',
            'street3' => '',
            'postcode' => '23848',
            'city' => 'New Taipei City',
            'state' => '',
            'country' => 'TW',
            'phone' => '+886-905-291-922',
            'extension' => '',
            'email' => 'mike@example.com',
            'tax_id' => '',
            'eori_number' => '',
            'is_residential' => false
        ];
    }
}
