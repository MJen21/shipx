<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomsDeclaration extends Model
{
    use HasFactory, Uuid;

    protected $hidden = ['shipment_id'];
    protected $with = ['items'];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function items()
    {
        return $this->hasMany(CustomsItem::class, 'shipment_id', 'shipment_id')->orderBy('line_number');
    }
}
