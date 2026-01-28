<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stylist extends Model
{
    protected $fillable = [
        'code', 'name', 'role', 'exp', 'rating', 'specialty', 'status', 'avatar', 'is_active', 'sort_order'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'float',
        'exp' => 'integer',
        'sort_order' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

}
