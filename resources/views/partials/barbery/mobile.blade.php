<div id="mobile-overlay" class="hidden fixed inset-0 bg-black/60 z-[9998] lg:hidden" onclick="toggleMobileMenu()">
</div>

<div id="mobile-menu"
    class="fixed top-0 left-0 bottom-0 w-72 bg-dark z-[9999] lg:hidden -translate-x-full transition-transform duration-300">
    <div class="p-6">
        <div class="flex items-center justify-between mb-8">
            <span class="font-display text-xl font-bold text-gold">Menu</span>
            <button onclick="toggleMobileMenu()" class="relative z-[10000] p-2 lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <a href="{{ route('site.home') }}#home" onclick="toggleMobileMenu()"
                class="block py-3 text-gray-300 hover:text-gold transition-colors border-b border-gray-800">Trang
                chủ</a>
            <a href="{{ route('site.home') }}#price" onclick="toggleMobileMenu()"
                class="block py-3 text-gray-300 hover:text-gold transition-colors border-b border-gray-800">Bảng giá</a>
            <a href="{{ route('site.home') }}#staff" onclick="toggleMobileMenu()"
                class="block py-3 text-gray-300 hover:text-gold transition-colors border-b border-gray-800">Thợ cắt</a>
            <a href="{{ route('site.home') }}#gallery" onclick="toggleMobileMenu()"
                class="block py-3 text-gray-300 hover:text-gold transition-colors border-b border-gray-800">Thư viện</a>
            <a href="{{ route('site.home') }}#contact" onclick="toggleMobileMenu()"
                class="block py-3 text-gray-300 hover:text-gold transition-colors border-b border-gray-800">Liên hệ</a>
        </div>

        <div class="mt-8 space-y-3">
            <a href="{{ route('site.lookup') }}" onclick="toggleMobileMenu()"
                class="w-full block text-center py-3 border border-gold text-gold rounded-full text-sm font-semibold hover:bg-gold hover:text-black transition-all">
                Tra cứu lịch
            </a>
            <a href="{{ route('site.booking') }}" onclick="toggleMobileMenu()"
                class="w-full block text-center py-3 gradient-gold text-black rounded-full text-sm font-semibold">
                Đặt lịch ngay
            </a>
        </div>
    </div>
</div>
