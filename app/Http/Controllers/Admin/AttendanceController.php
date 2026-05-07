<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Stylist;
use Illuminate\Http\Request;

class AttendanceController extends Controller //quản lý chấm công
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString()); //mở trang chấm công

        $stylists = Stylist::where('is_active', 1) //lấy dữ liệu stylist
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $attendances = Attendance::where('work_date', $date)  //chấm dữ liệu theo ngày
            ->get()
            ->keyBy('stylist_id');

        return view('admin.attendances.index', compact('date', 'stylists', 'attendances'));
    }

    public function store(Request $request)   //lưu trữ sau khi nhấn lưu
    {
        $request->validate([
            'work_date' => 'required|date',
            'attendances' => 'required|array',
        ]);

        foreach ($request->attendances as $stylistId => $data) {
            $status = $data['status'] ?? 'present';

            $workValue = match ($status) {
                'present' => 1,
                'half_day' => 0.5,
                'absent' => 0,
                'off' => 0,
                default => 1,
            };

            Attendance::updateOrCreate(
                [
                    'stylist_id' => $stylistId,
                    'work_date' => $request->work_date,
                ],
                [
                    'status' => $status,
                    'work_value' => $workValue,
                    'note' => $data['note'] ?? null,
                ]
            );
        }

        return redirect()
            ->route('admin.attendances.index', ['date' => $request->work_date])
            ->with('success', 'Đã lưu chấm công ngày ' . $request->work_date);
    }
}
