<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory, Uuid;

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }
}
