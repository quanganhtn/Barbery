{{-- Floating CTA Button (Mobile) --}}
<div class="fixed bottom-6 right-6 z-40 lg:hidden">
    <a href="{{ route('site.booking') }}"
        class="gradient-gold text-black w-14 h-14 rounded-full shadow-lg flex items-center justify-center pulse-gold">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </a>
</div>

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

{{-- Loading Overlay --}}
<div id="loading-overlay" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center">
    <div class="bg-dark rounded-2xl p-8 flex flex-col items-center border border-gray-800">
        <div class="w-12 h-12 border-4 border-gold border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-white">Đang xử lý...</p>
    </div>
</div>
