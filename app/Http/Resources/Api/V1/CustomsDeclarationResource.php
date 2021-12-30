<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomsDeclarationResource extends JsonResource
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
            'id' => $this->id,
            'created_at'     => $this->when($request->is('api/v1/customs/declaration*'), $this->created_at),
            'updated_at'     => $this->when($request->is('api/v1/customs/declaration*'), $this->updated_at),
            'invoice_date' => $this->invoice_date,
            'invoice_number' => $this->invoice_number,
            'incoterm' => $this->incoterm,
            'currency' => $this->currency,
            'items' => CustomsItemResource::collection($this->items)
        ];
    }
}
