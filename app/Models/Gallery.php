<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model //
{
    protected $fillable = [ //danh sách các cột cho phép gán dữ liệu
        'title',
        'subtitle',
        'image',
        'is_active',
    ];

    protected $casts = [ //ép kiểu dữ liệu
        'is_active' => 'boolean',
    ];
}
