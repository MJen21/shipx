<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomsItemResource extends JsonResource
{
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
            'created_at'     => $this->when($request->is('api/v1/customs/items*'), $this->created_at),
            'updated_at'     => $this->when($request->is('api/v1/customs/items*'), $this->updated_at),
            'line_number'    => $this->line_number,
            'description'    => $this->description,
            'quantity'       => $this->quantity,
            'quantity_unit'  => $this->quantity_unit,
            'net_weight'     => $this->net_weight,
            'gross_weight'   => $this->gross_weight,
            'weight_unit'    => $this->weight_unit,
            'unit_value'     => $this->unit_value,
            'tariff_number'  => $this->tariff_number,
            'origin_country' => $this->origin_country
        ];
    }
}
