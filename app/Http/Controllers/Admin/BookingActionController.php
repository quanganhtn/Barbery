<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingLookupCodeMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class BookingActionController extends Controller
{
    public function confirm($id)
    {
        $b = Booking::findOrFail($id);

        if ($b->status !== 'pending') {
            return back()->with([
                'message' => 'Chỉ có thể xác nhận lịch đang chờ.',
                'alert-type' => 'error'
            ]);
        }

        if (empty($b->lookup_code)) {
            $b->lookup_code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        $b->status = 'confirmed';
        $b->lookup_code_sent_at = now();
        $b->save();

        try {
            if (!empty($b->customer_email)) {
                Mail::to($b->customer_email)->send(new BookingLookupCodeMail($b));
            }
        } catch (\Throwable $e) {
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

    public function cancel($id)
    {
        $b = Booking::findOrFail($id);

        if (!in_array($b->status, ['pending', 'confirmed'])) {
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

    public function complete($id)
    {
        $b = Booking::findOrFail($id);

        if ($b->status !== 'confirmed') {
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
