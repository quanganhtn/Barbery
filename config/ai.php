<?php

return [
    /**
     * Cấu hình cho FastAPI / MediaPipe
     * Dùng để phân tích khuôn mặt
     */
    'base_url' => env('AI_SERVICE_URL', 'http://127.0.0.1:8001'),
    'timeout' => env('AI_SERVICE_TIMEOUT', 30),

    /**
     * Cấu hình cho Gemini chatbot
     */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models',
    ],
];
