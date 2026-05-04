<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Stylist;
use TCG\Voyager\Facades\Voyager;

class CatalogApiController extends Controller
{
    public function services() //danh sách dịch vụ
    {
        $rows = Service::query()
            ->where('is_active', true) //chỉ lấy dịch vụ đang bật
            ->orderBy('category_id') //sắp xếp theo danh mục
            ->orderBy('id')
            ->get(['id', 'code', 'name', 'price', 'duration_min', 'icon']); //lấy các cột cần thiết

        return response()->json(['ok' => true, 'data' => $rows]);
    }

    public function stylists() //danh sách stylists
    {
        $rows = Stylist::query()
            ->where('is_active', true)
            ->orderBy('id') //sắp xếp theo id
            ->get(['id', 'code', 'name', 'role', 'exp', 'rating', 'specialty', 'status', 'avatar']); //lấy các cột cần thiết

        $data = $rows->map(function ($s) {
            $path = $s->avatar ? str_replace('\\', '/', $s->avatar) : null; //nếu có avatar thì lấy đường dẫn

            return [
                'id' => $s->id,
                'code' => $s->code,
                'name' => $s->name,
                'role' => $s->role,
                'exp' => $s->exp,
                'rating' => $s->rating,
                'specialty' => $s->specialty,
                'status' => $s->status,
                'avatar_url' => $path ? \TCG\Voyager\Facades\Voyager::image($path) : null, //nếu có tạo url ảnh ko thì null
            ];
        });

        return response()->json(['ok' => true, 'data' => $data]);
    }
}
