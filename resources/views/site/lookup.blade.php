@extends('layouts.barbery')
@section('title', "Tra cứu lịch - Barbery")

@section('content')
    <div class="pt-20">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <a href="{{ route('site.home') }}"
               class="flex items-center gap-2 text-gray-400 hover:text-gold mb-8 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Quay lại trang chủ
            </a>

            <div class="text-center mb-10">
                <h1 class="font-display text-3xl sm:text-4xl font-bold text-white">Tra cứu lịch hẹn</h1>
                <p class="text-gray-400 mt-2">Nhập số điện thoại hoặc mã đặt lịch để xem thông tin</p>
            </div>

            <div class="bg-dark rounded-2xl p-6 border border-gray-800 mb-8">
                <form id="lookup-form" class="flex flex-col sm:flex-row gap-4">
                    <input type="text" id="lookup-input" required
                           class="flex-1 px-4 py-3 bg-darker border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-gold transition-colors"
                           placeholder="Nhập SĐT hoặc mã đặt lịch">
                    <button type="submit"
                            class="gradient-gold text-black px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition-opacity whitespace-nowrap">
                        Tra cứu
                    </button>
                </form>
            </div>

            <div id="lookup-results" class="hidden">
                <h2 class="font-semibold text-white mb-4">Kết quả tra cứu</h2>
                <div id="lookup-list" class="space-y-4"></div>
            </div>

            <div id="lookup-empty" class="hidden text-center py-12">
                <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-400">Không tìm thấy lịch hẹn nào</p>
            </div>
        </div>
    </div>
@endsection
