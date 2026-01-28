<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ===== Helpers =====
    private function normalizePhone(string $value): string
    {
        $raw = preg_replace('/\s+/', '', $value);
        // +84xxxxxxxxx hoặc 84xxxxxxxxx -> 0xxxxxxxxx
        $normalized = preg_replace('/^\+?84/', '0', $raw);
        return $normalized;
    }

    private function validatePhoneOrFail(string $value, callable $fail): void
    {
        $phone = $this->normalizePhone($value);

        if (!preg_match('/^\d+$/', $phone)) {
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

    // ===== (Optional) nếu bạn vẫn dùng endpoint này =====
    public function bookedSlots(Request $request)
    {
        $data = $request->validate([
            'date'       => ['required', 'date_format:Y-m-d'],
            'stylist_id'  => ['required', 'integer', 'exists:stylists,id'],
        ]);

        $slots = Booking::query()
            ->where('booking_date', $data['date'])
            ->where('stylist_id', $data['stylist_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('booking_time');

        return response()->json(['ok' => true, 'data' => $slots]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:120'],
            'customer_phone' => [
                'required',
                'string',
                'max:20',
                function ($attr, $value, $fail) {
                    $this->validatePhoneOrFail($value, $fail);
                },
            ],

            'service_ids'    => ['required', 'array', 'min:1'],
            'service_ids.*'  => ['integer', 'exists:services,id'],

            'stylist_id'     => ['required', 'integer', 'exists:stylists,id'],
            'booking_date'   => ['required', 'date_format:Y-m-d'],
            'booking_time'   => ['required', 'string', 'max:10'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        // ✅ Chuẩn hoá SĐT trước khi lưu + trước khi query chống spam
        $data['customer_phone'] = $this->normalizePhone($data['customer_phone']);

        $stylist = Stylist::findOrFail($data['stylist_id']);

        $serviceRows = Service::query()
            ->whereIn('id', $data['service_ids'])
            ->get(['id', 'name', 'price', 'duration_min']);

        if ($serviceRows->count() === 0) {
            abort(422, 'Vui lòng chọn ít nhất 1 dịch vụ hợp lệ.');
        }

        $serviceName   = $serviceRows->pluck('name')->join(', ');
        $totalPrice    = (int) $serviceRows->sum(fn($s) => (int) $s->price);
        $totalDuration = (int) $serviceRows->sum(fn($s) => (int) ($s->duration_min ?? 30));
        $firstServiceId = (int) $serviceRows->first()->id;

        $startAt = Carbon::createFromFormat('Y-m-d H:i', $data['booking_date'] . ' ' . $data['booking_time']);
        $endAt   = $startAt->copy()->addMinutes($totalDuration);

        $booking = DB::transaction(function () use (
            $data,
            $stylist,
            $firstServiceId,
            $serviceName,
            $totalPrice,
            $totalDuration,
            $startAt,
            $endAt
        ) {
            // ✅ CHỐNG SPAM THEO SĐT:
            // Chặn nếu số này đang có booking pending/confirmed (dù là thợ nào) và booking đó chưa kết thúc.
            $hasOpenBooking = Booking::query()
                ->where('customer_phone', $data['customer_phone'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereNotNull('end_at')
                ->where('end_at', '>=', now())   // future + in-progress đều bị chặn
                ->lockForUpdate()
                ->exists();

            if ($hasOpenBooking) {
                abort(429, 'Số điện thoại này đang có lịch hẹn pending/confirmed. Vui lòng huỷ/hoàn thành lịch cũ trước khi đặt thêm.');
            }

            // ✅ CHẶN TRÙNG SLOT THEO THỢ + TIME RANGE
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
                abort(409, 'Khung giờ này không còn trống theo thời lượng bạn chọn. Vui lòng chọn giờ khác.');
            }

            $code = $this->generateBookingCode();

            return Booking::create([
                'booking_code'   => $code,
                'customer_name'  => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],

                'service_id'     => $firstServiceId,
                'service_name'   => $serviceName,

                'stylist_id'     => $stylist->id,
                'stylist_name'   => $stylist->name,

                'booking_date'   => $data['booking_date'],
                'booking_time'   => $data['booking_time'],

                'start_at'           => $startAt,
                'end_at'             => $endAt,
                'total_duration_min' => $totalDuration,

                'notes'          => $data['notes'] ?? null,
                'status'         => 'pending',
                'total_price'    => $totalPrice,
            ]);
        });

        return response()->json([
            'ok' => true,
            'data' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'start_at' => $booking->start_at?->toIso8601String(),
                'end_at' => $booking->end_at?->toIso8601String(),
                'total_duration_min' => $booking->total_duration_min,
            ],
        ], 201);
    }

    private function generateBookingCode(): string
    {
        return 'BK' . strtoupper(Str::random(6));
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'max:50'],
        ]);

        $q = trim($data['q']);
        $qNoSpace = preg_replace('/\s+/', '', $q);
        $qPhone   = $this->normalizePhone($qNoSpace);

        $results = Booking::query()
            ->where(function ($query) use ($q, $qPhone) {
                $query->where('booking_code', $q)
                    ->orWhere('customer_phone', $qPhone)
                    ->orWhereRaw("REPLACE(customer_phone, ' ', '') = ?", [$qPhone]);
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json(['ok' => true, 'data' => $results]);
    }

    public function availableSlots(Request $request)
    {
        $data = $request->validate([
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'date'       => ['required', 'date_format:Y-m-d'],
            'duration'   => ['required', 'integer', 'min:15', 'max:480'],
        ]);

        $stylistId = (int) $data['stylist_id'];
        $date      = $data['date'];
        $duration  = (int) $data['duration'];

        $open  = Carbon::createFromFormat('Y-m-d H:i', $date . ' 08:00');
        $close = Carbon::createFromFormat('Y-m-d H:i', $date . ' 21:00');

        $bookings = Booking::query()
            ->where('stylist_id', $stylistId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('start_at')
            ->whereNotNull('end_at')
            ->whereDate('start_at', $date)
            ->get(['start_at', 'end_at']);

        $slots = [];

        for ($t = $open->copy(); $t->lt($close); $t->addMinutes(30)) {
            $slotStart = $t->copy();
            $slotEnd   = $t->copy()->addMinutes($duration);

            if ($slotEnd->gt($close)) continue;

            $overlap = $bookings->contains(function ($b) use ($slotStart, $slotEnd) {
                return $slotStart->lt($b->end_at) && $slotEnd->gt($b->start_at);
            });

            if (!$overlap) {
                $slots[] = $slotStart->format('H:i');
            }
        }

        return response()->json([
            'ok' => true,
            'data' => $slots, // để JS đơn giản
            'meta' => ['date' => $date, 'duration' => $duration],
        ]);
    }
}
