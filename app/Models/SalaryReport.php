<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryReport extends Model
{
    protected $fillable = [
        'stylist_id',
        'month',
        'year',
        'standard_work_days',
        'actual_work_days',
        'base_salary',
        'earned_base_salary',
        'total_bookings',
        'total_commission',
        'total_salary',
        'payment_status',
        'paid_at',
        'note',
    ];
    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }
}
