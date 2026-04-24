<?php

return [
    /**
     * Cấu hình cho FastAPI / MediaPipe
     * Dùng để phân tích khuôn mặt
     */
    'base_url' => env('AI_SERVICE_URL', 'http://127.0.0.1:8001'),
    'timeout' => env('AI_SERVICE_TIMEOUT', 30),


];
