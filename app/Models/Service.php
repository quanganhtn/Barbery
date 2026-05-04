<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'desc',
        'price',
        'duration_min',
        'icon',
        'image',
        'is_active',
        'is_featured',
        'is_hot',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_hot' => 'boolean',
        'category_id' => 'integer',
        'duration_min' => 'integer',
        'price' => 'integer',
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_service')
            ->withPivot(['service_name', 'price', 'duration_min'])
            ->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
