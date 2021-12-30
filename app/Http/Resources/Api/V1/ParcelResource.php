<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ParcelResource extends JsonResource
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
            'created_at'     => $this->when($request->segment(3) === 'parcels', $this->created_at),
            'updated_at'     => $this->when($request->segment(3) === 'parcels', $this->updated_at),
            'weight'         => $this->weight,
            'weight_unit'    => $this->weight_unit,
            'length'         => $this->length,
            'width'          => $this->width,
            'height'         => $this->height,
            'dimension_unit' => $this->dimension_unit
        ];
    }
}
