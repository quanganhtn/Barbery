<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingActionController extends Controller
{   //trạng thái
    public function confirm($id)//xác nhận lịch đặt
    {
        $b = Booking::findOrFail($id);//tìm bản ghi trong bảng booking
        if ($b->status !== 'pending') {//chỉ cho nhận khi ở trạng thái pending
            return back()->with([
                'message' => 'Chỉ có thể xác nhận lịch đang chờ.',
                'alert-type' => 'error'
            ]);
        }
        $b->update(['status' => 'confirmed']);//cập nhật trạng thái

        return back()->with([
            'message' => 'Đã xác nhận lịch ' . $b->booking_code,
            'alert-type' => 'success'
        ]);
    }

    public function cancel($id)//hủy lich
    {
        $b = Booking::findOrFail($id);
        if (!in_array($b->status, ['pending', 'confirmed'])) {//chỉ cho phép hủy khi 'pending', 'confirmed'
            return back()->with([
                'message' => 'Chỉ có thể hủy lịch đang chờ hoặc đã xác nhận.',
                'alert-type' => 'error'
            ]);
        }
        $b->update(['status' => 'cancelled']);

        return back()->with([
            'message' => 'Đã hủy lịch ' . $b->booking_code,
            'alert-type' => 'success'
        ]);
    }

    public function complete($id)//hoàn thành
    {
        $b = Booking::findOrFail($id);
        if ($b->status !== 'confirmed') {//chỉ cho phép khi 'confirmed'
            return back()->with([
                'message' => 'Chỉ có thể hoàn thành lịch đã xác nhận.',
                'alert-type' => 'error'
            ]);
        }
        $b->update(['status' => 'completed']);

        return back()->with([
            'message' => 'Đã hoàn thành lịch ' . $b->booking_code,
            'alert-type' => 'success'
        ]);
    }
}
