<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\CustomsDeclaration;
use App\Models\CustomsItem;
use App\Models\Parcel;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Mike Jen',
            'email' => 'mike@example.com'
        ]);

        $token = $user->createToken(env('APP_NAME'))->plainTextToken;
        echo $token . PHP_EOL;

        for ($i = 0; $i < 10; $i++) { 
            $shipper = Address::factory()->create([
                'user_id' => $user->id
            ]);
            $consignee = Address::factory()->create([
                'user_id' => $user->id,
                'name' => 'John Doe',
                'company' => '',
                'street1' => '2300 Traverwood Dr.',
                'street2' => '',
                'street3' => '',
                'postcode' => '48105',
                'city' => 'Ann Arbor',
                'state' => 'MI',
                'country' => 'US',
                'phone' => '+1 734-332-6500',
                'extension' => '',
                'email' => 'john@example.com',
                'tax_id' => '',
                'eori_number' => '',
                'is_residential' => false
            ]);
            $shipment = Shipment::factory()->create([
                'user_id' => $user->id,
                'shipper' => $shipper->id,
                'consignee' => $consignee->id,
                'status' => 'InfoReceived'
            ]);
            
            $customs_declaration = CustomsDeclaration::factory()->create([
                'user_id' => $user->id,
                'shipment_id' => $shipment->id
            ]);
    
            for ($line_number = 0; $line_number < 3; $line_number++) { 
                CustomsItem::factory()->create([
                    'user_id' => $user->id,
                    'shipment_id' => $shipment->id,
                    'line_number' => $line_number + 1
                ]);
            }
    
            Parcel::factory(3)->create([
                'user_id' => $user->id,
                'shipment_id' => $shipment->id
            ]);
        }

        for ($i = 0; $i < 10; $i++) { 
            $shipper = Address::factory()->create([
                'user_id' => $user->id
            ]);
            $consignee = Address::factory()->create([
                'user_id' => $user->id,
                'name' => 'John Doe',
                'company' => '',
                'street1' => '100 Century Avenue, Pudong',
                'street2' => '',
                'street3' => '',
                'postcode' => '200120',
                'city' => 'Shanghai',
                'state' => '',
                'country' => 'CN',
                'phone' => '+86-21-6133-7666',
                'extension' => '',
                'email' => 'john@example.com',
                'tax_id' => '',
                'eori_number' => '',
                'is_residential' => false
            ]);
            $shipment = Shipment::factory()->create([
                'user_id' => $user->id,
                'shipper' => $shipper->id,
                'consignee' => $consignee->id,
                'status' => 'InfoReceived'
            ]);
            
            $customs_declaration = CustomsDeclaration::factory()->create([
                'user_id' => $user->id,
                'shipment_id' => $shipment->id
            ]);
    
            for ($line_number = 0; $line_number < 3; $line_number++) { 
                CustomsItem::factory()->create([
                    'user_id' => $user->id,
                    'shipment_id' => $shipment->id,
                    'line_number' => $line_number + 1
                ]);
            }
    
            Parcel::factory(3)->create([
                'user_id' => $user->id,
                'shipment_id' => $shipment->id
            ]);
        }
    }
}
