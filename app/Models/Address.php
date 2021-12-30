<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory, Uuid;

    protected $guarded = [];
    protected $casts = [
        'is_residential' => 'boolean'
    ];

    public function scopeFilter($query, array $filters = [])
    {
        $page = (is_numeric($filters['page']) && $filters['page'] > 0) ? $filters['page'] : 1;
        $limit = (is_numeric($filters['limit']) && $filters['limit'] <= 40) ? $filters['limit'] : 40;
        $query->offset($limit * ($page - 1))->limit($limit);
        
        $query->when($filters['country'] ?? false, function ($query, $country) {
            $country = explode(',', $country);
            count($country) === 1
                ? $query->where('country', $country[0])
                : $query->whereIn('country', $country);
        });
    }
}
