<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-darker/95 backdrop-blur-sm border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">
            {{-- Logo --}}
            <a href="{{ route('site.home') }}" class="flex items-center gap-2">
                @php $logo = setting('site.logo'); @endphp

                @if ($logo)
                    <img src="{{ Voyager::image($logo) }}" class="w-10 h-10 rounded-full object-cover" alt="Logo">
                @else
                    <div class="w-10 h-10 rounded-full gradient-gold flex items-center justify-center">✓</div>
                @endif

                <span class="font-display text-xl lg:text-2xl font-bold text-gold">
                    {{ setting('site.title', "Gentlemen's") }}
                </span>
            </a>


            {{-- Desktop Menu --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="{{ route('site.home') }}#home" data-section="home"
                    class="nav-link text-sm font-medium text-gray-300 hover:text-gold transition-colors">Trang chủ</a>

                <a href="{{ route('site.home') }}#price" data-section="price"
                    class="nav-link text-sm font-medium text-gray-300 hover:text-gold transition-colors">Bảng giá</a>

                <a href="{{ route('site.home') }}#staff" data-section="staff"
                    class="nav-link text-sm font-medium text-gray-300 hover:text-gold transition-colors">Thợ cắt</a>

                <a href="{{ route('site.home') }}#gallery" data-section="gallery"
                    class="nav-link text-sm font-medium text-gray-300 hover:text-gold transition-colors">Hot Trend</a>

                <a href="{{ route('site.home') }}#contact" data-section="contact"
                    class="nav-link text-sm font-medium text-gray-300 hover:text-gold transition-colors">Liên hệ</a>
            </div>

            {{-- CTA Buttons --}}
            <div class="hidden lg:flex items-center gap-4">
                <a href="{{ route('site.lookup') }}"
                    class="text-sm font-medium text-gold hover:text-white transition-colors">Tra cứu lịch</a>
                <a href="{{ route('site.booking') }}"
                    class="gradient-gold text-black px-6 py-2.5 rounded-full text-sm font-semibold hover:opacity-90 transition-opacity pulse-gold">
                    Đặt lịch ngay
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <button id="mobile-menu-btn" onclick="toggleMobileMenu()" class="lg:hidden p-2 text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</nav>
