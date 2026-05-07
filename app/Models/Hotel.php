<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'city', 'country', 'description', 'address', 'phone', 'email',
        'star_rating', 'latitude', 'longitude', 'region_id',
        'images', 'amenities', 'metapolicy_struct', 'metapolicy_extra_info'
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
        'metapolicy_struct' => 'array',
    ];
}
