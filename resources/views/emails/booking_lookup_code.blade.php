<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Mã tra cứu lịch hẹn</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.7; color: #111827;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin-bottom: 16px;">Barbery - Xác nhận lịch hẹn</h2>

        <p>Xin chào <strong>{{ $booking->customer_name }}</strong>,</p>

        <p>Bạn đã đặt lịch thành công tại <strong>Barbery</strong>.</p>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin: 20px 0;">
            <p><strong>Mã đặt lịch:</strong> {{ $booking->booking_code }}</p>
            <p><strong>Mã tra cứu:</strong> {{ $booking->lookup_code }}</p>
            <p><strong>Dịch vụ:</strong> {{ $booking->service_name }}</p>
            <p><strong>Thợ cắt:</strong> {{ $booking->stylist_name }}</p>
            <p><strong>Ngày:</strong> {{ $booking->booking_date?->format('Y-m-d') }}</p>
            <p><strong>Giờ:</strong> {{ $booking->booking_time }}</p>
            <p><strong>Tổng thời gian:</strong> {{ $booking->total_duration_min }} phút</p>
            <p><strong>Tổng tiền:</strong> {{ number_format($booking->total_price, 0, ',', '.') }}đ</p>
        </div>

        <p>Vui lòng lưu lại <strong>mã đặt lịch</strong> và <strong>mã tra cứu</strong> để dùng khi cần tra cứu thông
            tin lịch hẹn.</p>

        <p>Trân trọng,<br>Barbery</p>
    </div>
</body>

</html>
