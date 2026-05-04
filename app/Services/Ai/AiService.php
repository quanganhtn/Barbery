<?php

namespace App\Services\Ai;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class AiService
{
    protected string $baseUrl;  //địa chỉ service AI
    protected int $timeout;  //tgian chơi AI trả lời

    public function __construct()  //chạy khi laravel tại AI
    {
        $this->baseUrl = rtrim((string) config('ai.base_url'), '/');  //lấy URL từ file config
        $this->timeout = (int) config('ai.timeout', 30);  //nếu config ko có thì mặc định là 30s
    }

    /**
     * Gọi FastAPI / MediaPipe để phân tích khuôn mặt
     */
    public function analyzeFace(UploadedFile $image): array
    {
        if (empty($this->baseUrl)) {  //kiểm tra rỗng
            throw new \RuntimeException('AI service URL đang trống.');
        }

        //Gửi ảnh sang FastAPI
        $response = Http::connectTimeout(5)   //chờ 5s ko kết nối được thì báo lỗi
            ->timeout($this->timeout)    //nếu kết nối được thì chời xử lý
            ->retry(1, 300)  //nếu xử lý lỗi thì chời 300s sử lý lại

            ->attach(
                'image',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            )
            ->post($this->baseUrl . '/analyze-face');

        //khi thấy bại
        if (!$response->successful()) {
            $body = $response->json();

            return [
                'status' => 'error',
                'message' => is_array($body)
                    ? ($body['detail'] ?? 'AI service error')
                    : 'AI service error',
            ];
        }
        //nếu thành công
        $data = $response->json();
        if (!is_array($data)) {
            return [
                'status' => 'error',
                'message' => 'AI response không hợp lệ.',
            ];
        }

        return $data;
    }
}
