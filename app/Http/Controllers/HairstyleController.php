<?php

namespace App\Http\Controllers;

use App\Services\Ai\AiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HairstyleController extends Controller
{
    public function index()
    {
        return view('site.hairstyle');
    }

    public function analyze(Request $request, AiService $ai): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ], [
            'image.required' => 'Vui lòng chọn ảnh.',
            'image.image' => 'File tải lên phải là ảnh.',
            'image.max' => 'Ảnh không được lớn hơn 5MB.',
        ]);

        try {
            $result = $ai->analyzeFace($request->file('image'));

            return response()->json([
                'status' => $result['status'] ?? 'error',
                'message' => $result['message'] ?? null,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể kết nối AI service: ' . $e->getMessage(),
            ], 500);
        }
    }
}
