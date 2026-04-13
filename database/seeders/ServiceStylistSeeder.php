<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Database\Seeder;

class ServiceStylistSeeder extends Seeder
{
    public function run(): void
    {
        Service::updateOrCreate(['code' => 'cut-basic'], [
            'name' => 'Cắt tóc nam',
            'price' => 80000,
            'duration_min' => 30,
            'icon' => '✂️',
            'sort_order' => 1,
        ]);

        Service::updateOrCreate(['code' => 'cut-fade'], [
            'name' => 'Cắt Fade',
            'price' => 100000,
            'duration_min' => 40,
            'icon' => '💈',
            'sort_order' => 2,
        ]);

        Service::updateOrCreate(['code' => 'combo-vip'], [
            'name' => 'Combo VIP (Cắt + Gội + Massage)',
            'price' => 150000,
            'duration_min' => 60,
            'icon' => '👑',
            'sort_order' => 3,
        ]);

        Stylist::updateOrCreate(['code' => 'stylist-1'], [
            'name' => 'Minh Phát',
            'role' => 'Master',
            'exp' => 8,
            'rating' => 4.9,
            'specialty' => 'Fade,Korean Style',
            'status' => 'available',
            'avatar' => 'M',
            'sort_order' => 1,
        ]);

        Stylist::updateOrCreate(['code' => 'stylist-2'], [
            'name' => 'Hoàng Nam',
            'role' => 'Senior',
            'exp' => 5,
            'rating' => 4.8,
            'specialty' => 'Classic,Uốn',
            'status' => 'busy',
            'avatar' => 'H',
            'sort_order' => 2,
        ]);
    }
}
