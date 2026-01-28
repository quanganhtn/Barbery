<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'customer_name',
        'customer_phone',
        'service_id',
        'service_name',
        'stylist_id',
        'stylist_name',
        'booking_date',
        'booking_time',
        'start_at',
        'end_at',
        'total_duration_min',
        'notes',
        'status',
        'total_price',
    ];

    protected $casts = [
        'booking_date' => 'date:Y-m-d',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'total_duration_min' => 'integer',
        'total_price' => 'integer',
    ];

    public function getDisplayNameAttribute()
    {
        return $this->booking_code . ' - ' . $this->customer_name;
    }


    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }
}
