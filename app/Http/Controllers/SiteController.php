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
        // 4 dịch vụ nổi bật (Hero bên phải)
        $featuredServices = Service::query()
            ->where('is_active', 1)
            ->where('is_featured', 1)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        // Danh mục hiển thị bảng giá (3 cột)
        $categories = ServiceCategory::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(3) // nếu bạn muốn đúng 3 cột (giống UI)
            ->get();

        // Services theo category_id (chỉ lấy những service có category_id)
        $servicesByCategory = Service::query()
            ->where('is_active', 1)
            ->whereNotNull('category_id') // tránh bị group null làm hỏng UI
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get()
            ->groupBy('category_id');


        //thợ cắt
        $stylists = Stylist::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        //tác phảm nổi bật
        $galleries = Gallery::where('is_active', 1)
            ->orderBy('sort_order')
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
