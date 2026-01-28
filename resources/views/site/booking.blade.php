@extends('layouts.barbery')
@section('title', 'Đặt lịch - Barbery')

@section('content')
    <div class="pt-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('site.home') }}"
                class="flex items-center gap-2 text-gray-400 hover:text-gold mb-8 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại trang chủ
            </a>

            <div class="text-center mb-10">
                <h1 class="font-display text-3xl sm:text-4xl font-bold text-white">Đặt lịch online</h1>
                <p class="text-gray-400 mt-2">Chọn dịch vụ và thời gian phù hợp với bạn</p>
            </div>

            {{-- Steps --}}
            <div class="flex items-center justify-center mb-10">
                <div class="flex items-center gap-2 sm:gap-4">
                    <div id="step-1-indicator"
                        class="step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold step-active">
                        1
                    </div>
                    <div class="w-8 sm:w-16 h-0.5 bg-gray-700"></div>
                    <div id="step-2-indicator"
                        class="step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold bg-gray-700 text-gray-400">
                        2
                    </div>
                    <div class="w-8 sm:w-16 h-0.5 bg-gray-700"></div>
                    <div id="step-3-indicator"
                        class="step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold bg-gray-700 text-gray-400">
                        3
                    </div>
                    <div class="w-8 sm:w-16 h-0.5 bg-gray-700"></div>
                    <div id="step-4-indicator"
                        class="step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold bg-gray-700 text-gray-400">
                        4
                    </div>
                </div>
            </div>

            {{-- Step Container --}}
            <div id="booking-steps-container">

                {{-- STEP 1 --}}
                <div id="booking-step-1" class="booking-step fade-in">
                    <h2 class="font-display text-xl font-bold text-white mb-6">Chọn dịch vụ</h2>
                    <div class="grid gap-4" id="service-list"></div>
                    <div class="flex justify-end mt-8">
                        <button onclick="nextStep()" id="btn-step1-next" disabled
                            class="gradient-gold text-black px-8 py-3 rounded-full font-semibold disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-90 transition-opacity">
                            Tiếp theo
                        </button>
                    </div>
                </div>


                {{-- STEP 2 --}}
                <div id="booking-step-2" class="booking-step hidden">
                    <h2 class="font-display text-xl font-bold text-white mb-6">Chọn thợ cắt</h2>
                    <div class="grid sm:grid-cols-2 gap-4" id="stylist-list"></div>
                    <div class="flex justify-between mt-8">
                        <button onclick="prevStep()"
                            class="border border-gray-600 text-gray-300 px-6 py-3 rounded-full font-semibold hover:border-gold hover:text-gold transition-all">
                            Quay lại
                        </button>
                        <button onclick="nextStep()" id="btn-step2-next" disabled
                            class="gradient-gold text-black px-8 py-3 rounded-full font-semibold disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-90 transition-opacity">
                            Tiếp theo
                        </button>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div id="booking-step-3" class="booking-step hidden">
                    <h2 class="font-display text-xl font-bold text-white mb-6">Chọn ngày giờ</h2>

                    <div class="mb-8">
                        <h3 class="text-sm font-medium text-gray-400 mb-4">Chọn ngày</h3>
                        <div class="flex gap-2 overflow-x-auto pb-4" id="date-list"></div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-400 mb-4">Chọn giờ</h3>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3" id="time-list"></div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button onclick="prevStep()"
                            class="border border-gray-600 text-gray-300 px-6 py-3 rounded-full font-semibold hover:border-gold hover:text-gold transition-all">
                            Quay lại
                        </button>
                        <button onclick="nextStep()" id="btn-step3-next" disabled
                            class="gradient-gold text-black px-8 py-3 rounded-full font-semibold disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-90 transition-opacity">
                            Tiếp theo
                        </button>
                    </div>
                </div>

                {{-- STEP 4 --}}
                <div id="booking-step-4" class="booking-step hidden">
                    <div class="grid lg:grid-cols-2 gap-8">

                        <div>
                            <h2 class="font-display text-xl font-bold text-white mb-6">Thông tin của bạn</h2>
                            <form id="booking-form" class="space-y-4" onsubmit="return false;">
                                <div>
                                    <label for="customer-name" class="block text-sm font-medium text-gray-300 mb-2">Họ
                                        tên <span class="text-red-500">*</span></label>
                                    <input type="text" id="customer-name" required
                                        class="w-full px-4 py-3 bg-dark border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-gold transition-colors"
                                        placeholder="Nhập họ tên">
                                </div>

                                <div>
                                    <label for="customer-phone" class="block text-sm font-medium text-gray-300 mb-2">Số
                                        điện thoại <span class="text-red-500">*</span></label>
                                    <input type="tel" id="customer-phone" required
                                        class="w-full px-4 py-3 bg-dark border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-gold transition-colors"
                                        placeholder="Nhập số điện thoại">
                                </div>

                                <div>
                                    <label for="customer-notes" class="block text-sm font-medium text-gray-300 mb-2">Ghi
                                        chú (tuỳ chọn)</label>
                                    <textarea id="customer-notes" rows="3"
                                        class="w-full px-4 py-3 bg-dark border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-gold transition-colors resize-none"
                                        placeholder="VD: Muốn cắt fade, tóc yếu không tẩy..."></textarea>
                                </div>
                            </form>
                        </div>

                        <div>
                            <h2 class="font-display text-xl font-bold text-white mb-6">Tóm tắt đặt lịch</h2>
                            <div class="bg-dark rounded-2xl p-6 border border-gray-800">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                        <span class="text-gray-400">Dịch vụ</span>
                                        <span id="summary-service" class="text-white font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                        <span class="text-gray-400">Thợ cắt</span>
                                        <span id="summary-stylist" class="text-white font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                        <span class="text-gray-400">Ngày</span>
                                        <span id="summary-date" class="text-white font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                        <span class="text-gray-400">Giờ</span>
                                        <span id="summary-time" class="text-white font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                        <span class="text-gray-400">Tổng thời gian</span>
                                        <span id="summary-duration" class="text-white font-medium">-</span>
                                    </div>

                                    <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                        <span class="text-gray-400">Giờ kết thúc</span>
                                        <span id="summary-endtime" class="text-white font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3">
                                        <span class="text-gray-400">Tổng tiền</span>
                                        <span id="summary-total" class="text-gold font-bold text-xl">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex justify-between mt-8">
                        <button onclick="prevStep()"
                            class="border border-gray-600 text-gray-300 px-6 py-3 rounded-full font-semibold hover:border-gold hover:text-gold transition-all">
                            Quay lại
                        </button>
                        <button onclick="submitBooking()" id="btn-submit-booking"
                            class="gradient-gold text-black px-8 py-3 rounded-full font-semibold hover:opacity-90 transition-opacity flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Xác nhận đặt lịch
                        </button>
                    </div>
                </div>
            </div>

            {{-- SUCCESS --}}
            <div id="booking-success" class="hidden text-center py-12 fade-in">
                <div class="w-20 h-20 gradient-gold rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mb-4">Đặt lịch thành công!</h2>
                <p class="text-gray-400 mb-6">Cảm ơn bạn đã đặt lịch. Chúng tôi sẽ xác nhận sớm nhất.</p>
                <div class="bg-dark rounded-2xl p-6 border border-gray-800 max-w-md mx-auto mb-8">
                    <p class="text-sm text-gray-400 mb-2">Mã đặt lịch của bạn</p>
                    <p id="booking-code-display" class="text-2xl font-bold text-gold">-</p>
                    <p class="text-sm text-gray-500 mt-2">Lưu mã này để tra cứu lịch hẹn</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('site.home') }}"
                        class="border border-gold text-gold px-6 py-3 rounded-full font-semibold hover:bg-gold hover:text-black transition-all">Về
                        trang chủ</a>
                    <button onclick="resetBooking()"
                        class="gradient-gold text-black px-6 py-3 rounded-full font-semibold hover:opacity-90 transition-opacity">
                        Đặt lịch mới
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection
