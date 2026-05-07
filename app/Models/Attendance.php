<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'stylist_id',
        'work_date',
        'status',
        'work_value',
        'note'
    ];
    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }
}
