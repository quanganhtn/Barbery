<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ChatbotController extends Controller
{
    /**
     * API gửi tin nhắn cho chatbot
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Giới hạn gửi tin nhắn theo IP
        $rateKey = 'chatbot:' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn gửi tin nhắn quá nhanh. Vui lòng thử lại sau ít phút.',
            ], 429);
        }

        RateLimiter::hit($rateKey, 60);

        // Lấy tin nhắn của người dùng
        $message = $data['message'];

        // Chuẩn hóa tin nhắn (xóa khoảng trắng, viết thường)
        $message = mb_strtolower(trim($message), 'UTF-8');

        // Dò từ khóa và trả lời phù hợp
        $reply = $this->getReply($message);

        return response()->json([
            'success' => true,
            'reply' => $reply,
        ]);
    }

    /**
     * Xử lý trả lời tự động theo từ khóa
     */
    private function getReply(string $message): string
    {
        // Định nghĩa các câu hỏi và câu trả lời
        $faq = [
            'greeting' => ['xin chào', 'chào', 'hello', 'hi', 'shop ơi', 'barbery ơi'],
            'opening_hours' => ['mở cửa', 'giờ mở cửa', 'làm việc', 'đóng cửa', 'mấy giờ', 'giờ'],
            'booking' => ['đặt lịch', 'booking', 'hẹn lịch', 'muốn đặt lịch'],
            'services' => ['dịch vụ', 'cắt tóc', 'gội đầu', 'cạo mặt', 'combo', 'giá'],
            'stylist' => ['stylist', 'thợ cắt', 'chọn thợ', 'chọn người cắt'],
            'lookup' => ['tra cứu', 'xem lịch', 'kiểm tra lịch', 'lịch hẹn'],
            'cancel_change' => ['hủy lịch', 'đổi lịch', 'đổi giờ', 'đổi lịch'],
            'hairstyle_ai' => ['gợi ý kiểu tóc', 'kiểu tóc', 'ai', 'phân tích khuôn mặt', 'tải ảnh'],
            'support' => ['tư vấn', 'nhân viên', 'liên hệ', 'hỗ trợ'],
        ];

        // Kiểm tra các từ khóa và trả lời phù hợp
        foreach ($faq as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    return $this->generateReply($intent);
                }
            }
        }

        // Nếu không khớp với từ khóa nào, trả lời fallback
        return 'Xin lỗi, tôi chưa hiểu rõ câu hỏi. Bạn có thể hỏi về đặt lịch, dịch vụ, stylist, tra cứu lịch hoặc gợi ý kiểu tóc bằng AI.';
    }

    /**
     * Trả lời theo từng intent
     */
    private function generateReply(string $intent): string
    {
        switch ($intent) {
            case 'greeting':
                return 'Xin chào! Mình là trợ lý Barbery. Mình có thể giúp bạn đặt lịch, tư vấn dịch vụ, hoặc gợi ý kiểu tóc.';

            case 'opening_hours':
                return 'Barbery mở cửa từ 8:00 đến 21:00 mỗi ngày. Bạn có thể đặt lịch trực tuyến hoặc gọi điện để đặt lịch.';

            case 'booking':
                return 'Để đặt lịch, bạn chỉ cần chọn dịch vụ, thợ cắt tóc, ngày và giờ. Sau đó xác nhận lịch để hoàn tất.';

            case 'services':
                return 'Barbery cung cấp các dịch vụ cắt tóc, gội đầu, cạo mặt và các combo chăm sóc tóc. Bạn có thể xem bảng dịch vụ chi tiết trên hệ thống.';

            case 'stylist':
                return 'Bạn có thể chọn thợ cắt tóc khi đặt lịch. Hệ thống sẽ hiển thị danh sách thợ cắt để bạn lựa chọn theo nhu cầu.';

            case 'lookup':
                return 'Bạn có thể vào trang Tra cứu lịch để kiểm tra lại lịch hẹn của mình.';

            case 'cancel_change':
                return 'Bạn có thể hủy hoặc thay đổi lịch hẹn bằng cách liên hệ với chúng tôi qua hotline hoặc trực tiếp tại trang Đặt lịch.';

            case 'hairstyle_ai':
                return 'Để nhận gợi ý kiểu tóc, bạn có thể tải ảnh khuôn mặt rõ nét lên hệ thống. Hệ thống AI sẽ phân tích và gợi ý kiểu tóc phù hợp.';

            case 'support':
                return 'Nếu bạn cần sự trợ giúp, vui lòng liên hệ với nhân viên của Barbery qua số điện thoại hoặc email hỗ trợ.';

            default:
                return 'Xin lỗi, tôi chưa hiểu rõ câu hỏi của bạn.';
        }
    }
}
