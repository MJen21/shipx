<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        
        return [
            'id'             => $this->id,
            'created_at'     => $this->when($request->is('api/v1/addresses*'), $this->created_at),
            'updated_at'     => $this->when($request->is('api/v1/addresses*'), $this->updated_at),
            'name'           => $this->name,
            'company'        => $this->company,
            'street1'        => $this->street1,
            'street2'        => $this->street2,
            'street3'        => $this->street3,
            'postcode'       => $this->postcode,
            'city'           => $this->city,
            'state'          => $this->state,
            'country'        => $this->country,
            'phone'          => $this->phone,
            'extension'      => $this->extension,
            'email'          => $this->email,
            'tax_id'         => $this->tax_id,
            'eori_number'    => $this->eori_number,
            'is_residential' => $this->is_residential
        ];
    }
}
