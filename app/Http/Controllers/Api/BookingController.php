<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    private function normalizePhone(string $value): string // đổi số điện thoại về dạng chuẩn
    {
        $raw = preg_replace('/\s+/', '', $value);
        $normalized = preg_replace('/^\+?84/', '0', $raw);
        return $normalized;
    }

    private function validatePhoneOrFail(string $value, callable $fail): void //kiểm tra tính hợp lệ
    {
        $phone = $this->normalizePhone($value);

        if (!preg_match('/^\d+$/', $phone)) { //phải là số
            $fail('Số điện thoại không hợp lệ.');
            return;
        }

        $len = strlen($phone);
        if ($len < 9 || $len > 11) {
            $fail('Số điện thoại không hợp lệ.');
            return;
        }

        if ($phone[0] !== '0') {
            $fail('Số điện thoại không hợp lệ.');
            return;
        }
    }

    /**
     * Sinh mã đặt lịch dạng BKXXXXXX
     */
    private function generateBookingCode(): string
    {
        do {
            $code = "BK" . strtoupper(Str::random(6));
        } while (Booking::where('booking_code', $code)->exists()); //kiểm tra nếu trùng random lại
        return $code;
    }

    /**
     * API tạo booking mới
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Thông tin khách
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => [
                'required',
                'string',
                'max:20',
                function ($attr, $value, $fail) {
                    $this->validatePhoneOrFail($value, $fail);
                },
            ],
            'customer_email' => ['required', 'email', 'max:150'],

            // Dịch vụ
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],

            // Lịch hẹn
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'booking_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['booking_date'] <= now()->format('Y-m-d')) {
            return response()->json([
                'message' => 'Chỉ được đặt từ ngày mai.',
            ], 422);
        }

        // Chuẩn hóa số điện thoại trước khi query/lưu
        $data['customer_phone'] = $this->normalizePhone($data['customer_phone']);

        //lấy Stylist
        $stylist = Stylist::findOrFail($data['stylist_id']);

        //chọn dịch vụ
        $serviceRows = Service::query()
            ->whereIn('id', $data['service_ids'])
            ->get(['id', 'name', 'price', 'duration_min']);

        if ($serviceRows->count() === 0) {
            return response()->json([
                'message' => 'Vui lòng chọn ít nhất 1 dịch vụ hợp lệ.',
            ], 422);
        }

        // Tính toán thông tin booking
        $serviceName = $serviceRows->pluck('name')->join(', ');
        $totalPrice = (int) $serviceRows->sum(fn($s) => (int) $s->price); //tổng tiền
        $totalDuration = (int) $serviceRows->sum(fn($s) => (int) $s->duration_min); //tổng tgian
        $firstServiceId = (int) $serviceRows->first()->id; //lưu vào cột service_id

        //tính thời gian
        $startAt = Carbon::createFromFormat('Y-m-d H:i', $data['booking_date'] . ' ' . $data['booking_time']);
        $endAt = $startAt->copy()->addMinutes($totalDuration);

        // Dùng transaction để tránh race condition
        $booking = DB::transaction(function () use (
            $data,
            $stylist,
            $serviceRows,
            $firstServiceId,
            $serviceName,
            $totalPrice,
            $totalDuration,
            $startAt,
            $endAt
        ) {
            /**
             * Chặn spam theo số điện thoại:
             * Nếu số này đang có booking pending/confirmed
             * và booking đó chưa kết thúc -> không cho đặt tiếp
             */
            $hasOpenBooking = Booking::query()
                ->where('customer_phone', $data['customer_phone'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereNotNull('end_at')
                ->where('end_at', '>=', now())
                ->lockForUpdate()
                ->exists();

            if ($hasOpenBooking) {
                return response()->json([
                    'message' => 'Số điện thoại này đang có lịch hẹn pending/confirmed. Vui lòng hoàn tất hoặc huỷ lịch cũ trước khi đặt thêm.',
                ], 429);
            }

            /**
             * Chặn trùng slot theo stylist + khoảng thời gian
             */
            $overlap = Booking::query()
                ->where('stylist_id', $data['stylist_id'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereNotNull('start_at')
                ->whereNotNull('end_at')
                ->where('start_at', '<', $endAt)
                ->where('end_at', '>', $startAt)
                ->lockForUpdate()
                ->exists();

            if ($overlap) {
                return response()->json([
                    'message' => 'Khung giờ này không còn trống theo thời lượng bạn chọn. Vui lòng chọn giờ khác.',
                ], 409);
            }

            $bookingCode = $this->generateBookingCode();

            // Tạo booking
            $booking = Booking::create([
                'booking_code' => $bookingCode,

                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'],

                // Giữ lại để tương thích dữ liệu/admin cũ
                'service_id' => $firstServiceId,
                'service_name' => $serviceName,

                'stylist_id' => $stylist->id,
                'stylist_name' => $stylist->name,

                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'total_duration_min' => $totalDuration,

                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
                'total_price' => $totalPrice,
            ]);

            // Lưu chi tiết nhiều dịch vụ vào bảng booking_service
            $booking->services()->attach(
                $serviceRows->mapWithKeys(function ($service) {
                    return [
                        $service->id => [
                            'service_name' => $service->name,
                            'price' => (int) $service->price,
                            'duration_min' => (int) $service->duration_min,
                        ],
                    ];
                })->toArray()
            );

            return $booking;
        });

        // Nếu transaction bên trong trả response lỗi thì return luôn
        if ($booking instanceof \Illuminate\Http\JsonResponse) {
            return $booking;
        }

        return response()->json([
            'ok' => true,
            'message' => 'Đặt lịch thành công. Vui lòng chờ xác nhận từ Barbery.',
            'data' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'status' => $booking->status,
                'start_at' => $booking->start_at?->toIso8601String(),
                'end_at' => $booking->end_at?->toIso8601String(),
                'total_duration_min' => $booking->total_duration_min,
            ],
        ], 201);
    }

    /**
     * API tra cứu booking
     * Hiện cho tra theo booking_code hoặc số điện thoại
     */
    public function lookup(Request $request)
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'max:50'],
        ]);

        // chuẩn hóa input
        $q = trim($data['q']);
        $qNoSpace = preg_replace('/\s+/', '', $q);
        $qPhone = $this->normalizePhone($qNoSpace);

        $results = Booking::query()
            ->where(function ($query) use ($q, $qPhone) {
                $query->where('booking_code', $q)
                    ->orWhere('customer_phone', $qPhone)
                    ->orWhereRaw("REPLACE(customer_phone, ' ', '') = ?", [$qPhone]);
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json([
            'ok' => true,
            'data' => $results,
        ]);
    }

    /**
     * API lấy giờ trống theo stylist + ngày + tổng duration
     */
    public function availableSlots(Request $request)
    {
        $data = $request->validate([
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'duration' => ['required', 'integer', 'min:15', 'max:480'],
        ]);

        $stylistId = (int) $data['stylist_id'];
        $date = $data['date'];
        $duration = (int) $data['duration'];

        $open = Carbon::createFromFormat('Y-m-d H:i', $date . ' 08:00');
        $close = Carbon::createFromFormat('Y-m-d H:i', $date . ' 20:00');

        //lấy booking
        $bookings = Booking::query()
            ->where('stylist_id', $stylistId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('start_at')
            ->whereNotNull('end_at')
            ->whereDate('start_at', $date)
            ->get(['start_at', 'end_at']);

        $slots = [];

        //slot cách nhau 30p
        for ($t = $open->copy(); $t->lt($close); $t->addMinutes(30)) {
            $slotStart = $t->copy();
            $slotEnd = $t->copy()->addMinutes($duration);

            //slot vượt quá giờ đóng cửa thì bỏ qua
            if ($slotEnd->gt($close)) {
                continue;
            }

            //kiểm tra trung lịch
            $overlap = $bookings->contains(function ($booking) use ($slotStart, $slotEnd) {
                return $slotStart->lt($booking->end_at) && $slotEnd->gt($booking->start_at);
            });

            //không trung thì hiện giờ trống
            if (!$overlap) {
                $slots[] = $slotStart->format('H:i');
            }
        }

        return response()->json([
            'ok' => true,
            'data' => $slots,
            'meta' => [
                'date' => $date,
                'duration' => $duration,
            ],
        ]);
    }
}
