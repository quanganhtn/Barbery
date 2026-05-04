<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $fillable = [ //danh sách các cột được gán dữ liệu hàng loạt
        'booking_code',

        'customer_name',
        'customer_phone',
        'customer_email',

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


    protected $casts = [ //ép kiểu dữ liệu
        'booking_date' => 'date:Y-m-d',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'total_duration_min' => 'integer',
        'total_price' => 'integer',
    ];


    public function getDisplayNameAttribute() //tóm tắt thông tin
    {
        return $this->booking_code . ' - ' . $this->customer_name;
    }

    public function stylist() //Quan hệ 1 booking - stylist
    {
        return $this->belongsTo(Stylist::class);
    }

    public function services() //Quan hệ nhiều-nhiều booking - services
    {
        return $this->belongsToMany(Service::class, 'booking_service')
            ->withPivot(['service_name', 'price', 'duration_min'])
            ->withTimestamps();
    }
}
