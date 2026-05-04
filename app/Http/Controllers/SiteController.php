<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Service;
use App\Models\Stylist;
use App\Models\ServiceCategory;

class SiteController extends Controller
{
    public function home()
    {
        // 4 dịch vụ nổi bật
        $featuredServices = Service::query()
            ->where('is_active', 1) //dịch vụ đang bật
            ->where('is_featured', 1) //dịch vụ đánh dấu nổi bật
            ->orderBy('id')
            ->limit(4)
            ->get();

        // Danh mục hiển thị bảng giá
        $categories = ServiceCategory::query()
            ->where('is_active', 1) //dịch vụ đang bật
            ->orderBy('id')
            ->limit(3)
            ->get();

        // Dịch vụ theo danh mục
        $servicesByCategory = Service::query()
            ->where('is_active', 1)
            ->whereNotNull('category_id') // tránh bị group null làm hỏng UI
            ->orderBy('category_id')
            ->orderBy('id')
            ->get()
            ->groupBy('category_id');


        //thợ cắt
        $stylists = Stylist::query()
            ->where('is_active', 1)
            ->orderBy('id')
            ->limit(8)
            ->get();

        //tác phảm nổi bật
        $galleries = Gallery::where('is_active', 1)
            ->orderBy('id')
            ->limit(10)
            ->get();

        return view('site.home', compact(
            'featuredServices',
            'categories',
            'servicesByCategory',
            'stylists',
            'galleries',
        ));
    }

    public function booking()
    {
        return view('site.booking');
    }

    public function lookup()
    {
        return view('site.lookup');
    }
}
