<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stylist extends Model
{
    protected $fillable = [
        'code',
        'name',
        'role',
        'exp',
        'rating',
        'base_salary',
        'specialty',
        'status',
        'avatar',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'float',
        'exp' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
