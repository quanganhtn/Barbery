<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingCodeMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class BookingActionController extends Controller //quản lý trạng thái booking
{
    public function confirm($id) //xác nhận
    {
        $b = Booking::findOrFail($id); //lấy giữ liệu booking

        if ($b->status !== 'pending') { //kiểm tra trạng thái
            return back()->with([
                'message' => 'Chỉ có thể xác nhận lịch đang chờ.',
                'alert-type' => 'error'
            ]);
        }

        $b->status = 'confirmed';
        $b->save(); //update trạng thái

        try {
            if (!empty($b->customer_email)) {
                Mail::to($b->customer_email)->send(new BookingCodeMail($b));
            } //gửi mail
        } catch (\Throwable $e) { //kiểm tra lỗi mail thì thông báo
            report($e);

            return back()->with([
                'message' => 'Đã xác nhận lịch nhưng gửi email thất bại.',
                'alert-type' => 'error'
            ]);
        }

        return back()->with([
            'message' => 'Đã xác nhận lịch ' . $b->booking_code . ' và gửi email cho khách.',
            'alert-type' => 'success'
        ]);
    }

    public function cancel($id) //hủy lịch
    {
        $b = Booking::findOrFail($id);

        if (!in_array($b->status, ['pending', 'confirmed'])) {
            return back()->with([
                'message' => 'Chỉ có thể hủy lịch đang chờ hoặc đã xác nhận.',
                'alert-type' => 'error'
            ]);
        }

        $b->update(['status' => 'cancelled']);   //chuyển đổi trạng thái

        return back()->with([
            'message' => 'Đã hủy lịch ' . $b->booking_code,
            'alert-type' => 'success'
        ]);
    }

    public function complete($id)
    {
        $b = Booking::findOrFail($id);   //kiểm tra booking

        if ($b->status !== 'confirmed') { //nếu trang thái đã được xác nhận sau khi cắt xong thì sẽ nhấn hoàn thành
            return back()->with([
                'message' => 'Chỉ có thể hoàn thành lịch đã xác nhận.',
                'alert-type' => 'error'
            ]);
        }

        $b->update(['status' => 'completed']); //đổi trạng th

        return back()->with([
            'message' => 'Đã hoàn thành lịch ' . $b->booking_code,
            'alert-type' => 'success'
        ]);
    }
}
