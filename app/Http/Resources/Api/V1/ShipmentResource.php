<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Address;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ShipmentResource extends JsonResource
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
            'id'                  => $this->id,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'status'              => $this->status,
            'date'                => $this->date,
            'type'                => $this->type,
            'purpose'             => $this->purpose,
            'contents'            => $this->contents,
            'service'             => $this->service,
            'tracking_number'     => $this->tracking_number,
            'label_url'           => $this->when($this->tracking_number ?: false, url(Storage::url("labels/{$this->tracking_number}.pdf"))),
            'shipper'             => $this->shipper,
            'consignee'           => $this->consignee,
            'customs_declaration' => $this->when($this->type === 'Non-Doc', new CustomsDeclarationResource($this->customs_declaration)),
            'parcels'             => ParcelResource::collection($this->parcels)
        ];
    }
}
