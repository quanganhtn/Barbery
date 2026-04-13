<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /**
     * Các cột cho phép gán hàng loạt
     */
    protected $fillable = [
        'booking_code',
        'lookup_code',
        'lookup_code_sent_at',

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

    /**
     * Ép kiểu dữ liệu
     */
    protected $casts = [
        'booking_date' => 'date:Y-m-d',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'lookup_code_sent_at' => 'datetime',
        'total_duration_min' => 'integer',
        'total_price' => 'integer',
    ];

    /**
     * Tên hiển thị trong admin
     */
    public function getDisplayNameAttribute()
    {
        return $this->booking_code . ' - ' . $this->customer_name;
    }

    /**
     * Quan hệ 1 booking - service chính
     * Giữ lại để tương thích dữ liệu cũ
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Quan hệ 1 booking - stylist
     */
    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    /**
     * Quan hệ nhiều-nhiều booking - services
     * Vì 1 booking có thể có nhiều dịch vụ
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_service')
            ->withPivot(['service_name', 'price', 'duration_min'])
            ->withTimestamps();
    }
}
