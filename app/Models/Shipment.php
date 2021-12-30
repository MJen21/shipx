<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory, Uuid;

    protected $with = [
        'shipper',
        'consignee',
        'customs_declaration',
        'parcels'
    ];

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['type'] ?? false, fn($query, $type) => $query->where('type', $type));
        $query->when($filters['product'] ?? false, fn($query, $product) => $query->where('product', $product));
        $query->when($filters['origin'] ?? false, function ($query, $origin) {
            $origin = explode(',', $origin);
            count($origin) === 1
            ? $query->whereHas('shipper', fn($query) => $query->where('country', $origin[0]))
            : $query->whereHas('shipper', fn($query) => $query->whereIn('country', $origin));
        });
        $query->when($filters['destination'] ?? false, function ($query, $destination) {
            $destination = explode(',', $destination);
            count($destination) === 1
            ? $query->whereHas('consignee', fn($query) => $query->where('country', $destination[0]))
            : $query->whereHas('consignee', fn($query) => $query->whereIn('country', $destination));
        });
        $query->when($filters['status'] ?? false, function ($query, $status) {
            $status = explode(',', $status);
            count($status) === 1
            ? $query->where('status', $status[0])
            : $query->whereIn('status', $status);
        });
        
        $limit = (isset($filters['limit']) && is_numeric($filters['limit']) && $filters['limit'] <= 40) ? $filters['limit'] : 40;
        $page = (isset($filters['page']) && is_numeric($filters['page']) && $filters['page'] > 0) ? $filters['page'] : 1;
        $query->limit($limit)->offset($limit * ($page - 1));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shipper()
    {
        return $this->hasOne(Address::class, 'id', 'shipper');
    }
    
    public function consignee()
    {
        return $this->hasOne(Address::class, 'id', 'consignee');
    }

    public function customs_declaration()
    {
        return $this->hasOne(CustomsDeclaration::class, 'shipment_id', 'id');
    }
    
    public function customs_items()
    {
        return $this->hasMany(CustomsItem::class, 'id', 'shipment_id')->orderBy('line_number');
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class)->orderBy('checkpoint_time');
    }
}
