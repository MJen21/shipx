<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckpointResource extends JsonResource
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
            'slug'            => $this->slug,
            'checkpoint_time' => $this->checkpoint_time,
            'code'            => $this->code,
            'message'         => $this->message,
            'location'        => $this->location,
            'country_iso3'    => $this->country_iso3,
            'country_name'    => $this->country_name,
            'tag'             => $this->tag,
            'subtag'          => $this->subtag,
            'subtag_message'  => $this->subtag_message,
        ];
    }
}
