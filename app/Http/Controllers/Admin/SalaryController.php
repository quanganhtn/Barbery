<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Booking;
use App\Models\SalaryReport;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index(Request $request)   //xem danh sách bảng lương
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $reports = SalaryReport::with('stylist')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('stylist_id')
            ->get();

        return view('admin.salaries.index', compact('reports', 'month', 'year'));
    }

    public function calculate(Request $request)   //tính lương
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $standardWorkDays = (float) $request->input('standard_work_days', 26); //số ngày công tháng

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $stylists = Stylist::where('is_active', 1)->get(); //lấy các stylist đang hoạt động

        foreach ($stylists as $stylist) {   //duyệt từng stylist
            $actualWorkDays = (float) Attendance::where('stylist_id', $stylist->id)
                ->whereBetween('work_date', [$startDate, $endDate])
                ->sum('work_value');

            //lấy số booking hoàn thành
            $bookings = Booking::with('services')
                ->where('stylist_id', $stylist->id)
                ->where('status', 'completed')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->get();
            $totalBookings = $bookings->count();  //đếm số booking hoàn thành

            //tính lương dịch vụ
            $totalCommission = 0;

            foreach ($bookings as $booking) {
                foreach ($booking->services as $service) {
                    $totalCommission += (int) $service->stylists_commission;
                }
            }

            //tính lương cơ bản
            $baseSalary = (int) ($stylist->base_salary ?? 3000000);
            if ($standardWorkDays > 0) {
                $earnedBaseSalary = (int) round($baseSalary / $standardWorkDays * $actualWorkDays);
            } else {
                $earnedBaseSalary = 0;
            }

            //tổng lương
            $totalSalary = $earnedBaseSalary + $totalCommission;


            SalaryReport::updateOrCreate(
                [
                    'stylist_id' => $stylist->id,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'standard_work_days' => $standardWorkDays,
                    'actual_work_days' => $actualWorkDays,
                    'base_salary' => $baseSalary,
                    'earned_base_salary' => $earnedBaseSalary,
                    'total_bookings' => $totalBookings,
                    'total_commission' => $totalCommission,
                    'total_salary' => $totalSalary,
                    'payment_status' => 'unpaid',
                ]
            );
        }

        return redirect()
            ->route('admin.salaries.index', [
                'month' => $month,
                'year' => $year,
            ])
            ->with('success', 'Đã tính lương tháng ' . $month . '/' . $year);
    }

    public function show($id)
    {
        $report = SalaryReport::with('stylist')->findOrFail($id);

        $startDate = Carbon::create($report->year, $report->month, 1)
            ->startOfMonth()
            ->toDateString();

        $endDate = Carbon::create($report->year, $report->month, 1)
            ->endOfMonth()
            ->toDateString();

        $attendances = Attendance::where('stylist_id', $report->stylist_id)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->orderBy('work_date')
            ->paginate(10);

        $bookings = Booking::with('services')
            ->where('stylist_id', $report->stylist_id)
            ->where('status', 'completed')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->orderBy('booking_date')
            ->get();

        $serviceStats = [];

        foreach ($bookings as $booking) {
            foreach ($booking->services as $service) {
                $serviceName = $service->pivot->service_name ?? $service->name;
                $commission = (int) $service->stylists_commission;

                if (!isset($serviceStats[$serviceName])) {
                    $serviceStats[$serviceName] = [
                        'name' => $serviceName,
                        'count' => 0,
                        'commission' => $commission,
                        'total' => 0,
                    ];
                }

                $serviceStats[$serviceName]['count']++;
                $serviceStats[$serviceName]['total'] += $commission;
            }
        }

        return view('admin.salaries.show', compact(
            'report',
            'attendances',
            'bookings',
            'serviceStats'
        ));
    }

    public function markPaid($id)   //đánh dấu thanh toán lương
    {
        $report = SalaryReport::findOrFail($id);

        $report->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Đã đánh dấu đã thanh toán lương.');
    }
}
