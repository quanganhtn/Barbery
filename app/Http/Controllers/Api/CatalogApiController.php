<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Stylist;
use TCG\Voyager\Facades\Voyager;

class CatalogApiController extends Controller
{
    public function services()
    {
        $rows = Service::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'code', 'name', 'price', 'duration_min', 'icon']);

        return response()->json(['ok' => true, 'data' => $rows]);
    }

    public function stylists()
    {
        $rows = Stylist::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'code', 'name', 'role', 'exp', 'rating', 'specialty', 'status', 'avatar']);

        $data = $rows->map(function ($s) {
            $path = $s->avatar ? str_replace('\\', '/', $s->avatar) : null;

            return [
                'id' => $s->id,
                'code' => $s->code,
                'name' => $s->name,
                'role' => $s->role,
                'exp' => $s->exp,
                'rating' => $s->rating,
                'specialty' => $s->specialty,
                'status' => $s->status,
                'avatar_url' => $path ? \TCG\Voyager\Facades\Voyager::image($path) : null,
            ];
        });

        return response()->json(['ok' => true, 'data' => $data]);
    }
}
