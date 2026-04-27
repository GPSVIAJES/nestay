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
        'id', 'name', 'description', 'address', 'phone', 'email',
        'star_rating', 'latitude', 'longitude', 'region_id',
        'images', 'amenities'
    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
    ];
}
