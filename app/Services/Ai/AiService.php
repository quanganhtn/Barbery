<?php

namespace App\Services\Ai;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class AiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('ai.base_url'), '/');
        $this->timeout = (int) config('ai.timeout', 30);
    }

    /**
     * Gọi FastAPI / MediaPipe để phân tích khuôn mặt
     */
    public function analyzeFace(UploadedFile $image): array
    {
        if (empty($this->baseUrl)) {
            throw new \RuntimeException('AI service URL đang trống.');
        }

        $response = Http::connectTimeout(5)
            ->timeout($this->timeout)
            ->retry(1, 300)
            ->attach(
                'image',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            )
            ->post($this->baseUrl . '/analyze-face');

        if (!$response->successful()) {
            $body = $response->json();

            return [
                'status' => 'error',
                'message' => is_array($body)
                    ? ($body['detail'] ?? 'AI service error')
                    : 'AI service error',
            ];
        }

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
