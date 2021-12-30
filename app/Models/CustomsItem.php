<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomsItem extends Model
{
    use HasFactory, Uuid;

    protected $guarded = [];
    protected $casts = [
        'line_number'  => 'integer',
        'quantity'     => 'integer',
        'net_weight'   => 'integer',
        'gross_weight' => 'float',
        'unit_value'   => 'float',
        'height'       => 'float',
    ];


    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function customs_declaration()
    {
        return $this->belongsTo(CustomsDeclaration::class, 'shipment_id', 'shipment_id');
    }
}
