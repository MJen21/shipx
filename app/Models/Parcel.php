<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    use HasFactory, Uuid;

    protected $guarded = [];
    protected $casts = [
        'weight' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }
}
