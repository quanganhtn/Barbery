<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens,  //tạo token API
        HasFactory,   //tạo user mẫu/test
        Notifiable;   //nhận thông báo

    protected $fillable = [ //danh sách các cột được phép gán
        'name',
        'email',
        'password',
        'role_id',
        'avatar',
        'settings',
    ];

    protected $hidden = [  //ẩn dữ liệu
        'password',
        'remember_token',
    ];

    protected $casts = [   //ép kiểu dữ liệu
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'settings' => 'array',
    ];
}
