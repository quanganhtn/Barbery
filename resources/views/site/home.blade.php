@extends('layouts.barbery')
@section('title', setting('site.title', "Gentlemen's Barbershop"))

@section('content')
    @php
        use TCG\Voyager\Facades\Voyager;

        // helper: initials
        $initials = function ($name) {
            $name = trim((string) $name);
            if ($name === '') {
                return 'B';
            }
            $parts = preg_split('/\s+/', $name);
            $first = mb_substr($parts[0] ?? '', 0, 1);
            $last = mb_substr($parts[count($parts) - 1] ?? '', 0, 1);
            $ini = mb_strtoupper($first . ($last ?: ''));
            return $ini ?: 'B';
        };

        // helper: render stars from rating float (0-5) => ★★★★☆
        $renderRatingStars = function ($rating) {
            $rating = (float) ($rating ?? 5);
            $rating = max(0, min(5, $rating));
            $full = (int) floor($rating);
            $empty = 5 - $full;
            return str_repeat('★', $full) . str_repeat('☆', $empty);
        };
    @endphp

    {{-- =========================
   HERO
========================= --}}
    <section id="home" class="relative min-h-screen flex items-center">
        <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-black"></div>
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 left-10 w-72 h-72 bg-gold rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-gold rounded-full filter blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                {{-- Left --}}
                <div class="fade-in">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gold/10 border border-gold/30 rounded-full mb-6">
                        <span class="w-2 h-2 bg-gold rounded-full animate-pulse"></span>
                        <span class="text-sm text-gold">
                            Mở cửa • {{ setting('site.open_hours', '8:00 - 21:00') }}
                        </span>
                    </div>

                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                        <span class="text-white">{{ setting('home.hero_title_1', 'Phong cách') }}</span><br>
                        <span class="text-gold">{{ setting('home.hero_title_2', 'Đẳng cấp') }}</span><br>
                        <span class="text-white">{{ setting('home.hero_title_3', 'Dành cho') }}</span>
                        <span class="text-gold"> {{ setting('home.hero_title_4', 'Quý ông') }}</span>
                    </h1>

                    <p class="text-lg text-gray-400 mb-8 max-w-lg">
                        {{ setting('home.hero_desc', 'Trải nghiệm dịch vụ cắt tóc nam cao cấp với đội ngũ stylist chuyên nghiệp. Đặt lịch online, không cần chờ đợi.') }}
                    </p>


                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl">
                        <a href="{{ route('site.booking') }}"
                            class="gradient-gold text-black h-16 rounded-full text-lg font-semibold hover:opacity-90 transition-opacity flex items-center justify-center gap-2 pulse-gold w-full">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ setting('home.cta_booking', 'Đặt lịch ngay') }}</span>
                        </a>

                        <a href="tel:{{ preg_replace('/\s+/', '', setting('site.phone', '0909123456')) }}"
                            class="border-2 border-gold text-gold h-16 rounded-full text-lg font-semibold hover:bg-gold hover:text-black transition-all flex items-center justify-center gap-2 w-full">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>{{ setting('home.cta_call', 'Gọi nhanh') }}</span>
                        </a>

                        <a href="{{ route('hairstyle.index') }}"
                            class="border-2 border-gold text-gold h-16 rounded-full text-lg font-semibold hover:bg-gold hover:text-black transition-all flex items-center justify-center gap-2 w-full">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.264a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Gợi ý kiểu tóc</span>

                        </a>
                    </div>
                    <div class="flex items-center gap-8 mt-12">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gold">{{ setting('site.total_customers', '5000') }}+</div>
                            <div class="text-sm text-gray-500">Khách hàng</div>
                        </div>

                        <div class="w-px h-12 bg-gray-700"></div>

                        <div class="text-center">
                            <div class="text-3xl font-bold text-gold">{{ setting('site.rating', '4.9') }}</div>
                            <div class="text-sm text-gray-500">Đánh giá</div>
                        </div>

                        <div class="w-px h-12 bg-gray-700"></div>

                        <div class="text-center">
                            <div class="text-3xl font-bold text-gold">{{ setting('site.experience_years', 8) }}+</div>
                            <div class="text-sm text-gray-500">Năm kinh nghiệm</div>
                        </div>
                    </div>
                </div>

                {{-- Right: Featured services (DB) --}}
                <div class="relative hidden lg:block">
                    <div class="absolute -top-10 -right-10 w-80 h-80 bg-gold/20 rounded-full filter blur-3xl"></div>
                    <div
                        class="relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-8 border border-gray-700">
                        <div class="grid grid-cols-2 gap-4">
                            @forelse ($featuredServices as $sv)
                                <div class="bg-dark rounded-2xl p-6 card-hover">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden mb-4 border border-gold/30">
                                        <img src="{{ $sv->image ? Voyager::image($sv->image) : asset('images/service-default.jpg') }}"
                                            alt="{{ $sv->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <h3 class="font-semibold text-white mb-1">{{ $sv->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Từ {{ number_format((int) $sv->price, 0, ',', '.') }}đ
                                    </p>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =========================
   PRICE (DB)
========================= --}}
    <section id="price" class="py-20 bg-darker">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-gold text-sm font-semibold tracking-wider uppercase">Bảng giá</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mt-4">Giá dịch vụ</h2>
                <p class="text-gray-400 mt-4">Tất cả giá đã bao gồm gội đầu và sấy tạo kiểu cơ bản</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                @forelse ($categories as $cat)
                    @php
                        $items = $servicesByCategory[$cat->id] ?? collect();
                        $isCombo = ($cat->slug ?? '') === 'combo';
                    @endphp

                    <div
                        class="bg-dark rounded-2xl p-8 border {{ $isCombo ? 'border-2 border-gold ring-2 ring-gold/60' : 'border border-gray-800' }}">

                        <h3 class="font-display text-2xl font-bold text-white mb-6">{{ $cat->name }}</h3>

                        @if ($items->isEmpty())
                            <p class="text-gray-500">Chưa có dịch vụ.</p>
                        @else
                            <div class="space-y-4">
                                @foreach ($items as $sv)
                                    <div
                                        class="flex items-center justify-between py-3 border-b border-gray-800 last:border-b-0">
                                        <div>
                                            <div class="text-gray-300 font-medium">{{ $sv->name }}</div>

                                            {{-- dòng mô tả nhỏ dưới tên --}}
                                            @if (!empty($sv->desc))
                                                <div class="text-xs text-gray-500">{{ $sv->desc }}</div>
                                            @else
                                                <div class="text-xs text-gray-500">~{{ (int) $sv->duration_min }} phút
                                                </div>
                                            @endif
                                        </div>

                                        <div class="text-gold font-bold">
                                            {{ number_format((int) $sv->price, 0, ',', '.') }}đ
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                @endforelse
            </div>

        </div>
    </section>

    {{-- =========================
   STAFF (DB stylists)
========================= --}}
    <section id="staff" class="py-20 bg-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-gold text-sm font-semibold tracking-wider uppercase">Đội ngũ</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mt-4">
                    {{ setting('home.staff_title', 'Stylist chuyên nghiệp') }}
                </h2>
                <p class="text-gray-400 mt-4">
                    {{ setting('home.staff_desc', 'Đội ngũ thợ cắt tóc giàu kinh nghiệm, được đào tạo bài bản') }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse ($stylists as $st)
                    @php
                        $name = $st->name ?? 'Stylist';
                        $role = $st->role ?? 'Senior';
                        $exp = (int) ($st->exp ?? 0);
                        $rating = (float) ($st->rating ?? 5);
                        $spec = $st->specialty ?? '';
                        $avatar = $st->avatar ?? null;
                    @endphp

                    <div class="bg-darker rounded-2xl p-6 border border-gray-800 card-hover text-center">
                        <div class="mx-auto mb-4 w-20 h-20 rounded-full overflow-hidden border border-gold/30 bg-gold/10">
                            @if ($avatar)
                                <img src="{{ Voyager::image($avatar) }}" alt="{{ $name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div
                                    class="w-full h-full gradient-gold flex items-center justify-center text-2xl font-bold text-black">
                                    {{ $initials($name) }}
                                </div>
                            @endif
                        </div>

                        <h3 class="font-semibold text-lg text-white mb-1">{{ $name }}</h3>

                        <span
                            class="inline-block text-xs px-3 py-1 rounded-full mb-3 bg-gold/10 text-gold border border-gold/30">
                            {{ $role }} • {{ $exp }} năm
                        </span>

                        <p class="text-sm text-gray-500 mb-4">{{ $spec }}</p>

                        <div class="flex items-center justify-center gap-2 text-gold mb-4">
                            <span>⭐</span><span class="font-semibold">{{ number_format($rating, 1) }}</span>
                        </div>

                        <a href="{{ route('site.booking', ['stylist_id' => $st->id]) }}"
                            class="w-full inline-block border border-gold text-gold py-2 rounded-full text-sm font-semibold hover:bg-gold hover:text-black transition-all">
                            Chọn & Đặt lịch
                        </a>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>

    {{-- =========================
   GALLERY (DB galleries)
========================= --}}
    <section id="gallery" class="py-20 bg-darker">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-gold text-sm font-semibold tracking-wider uppercase">Các kiểu tóc Hot Trend</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mt-4">
                    {{ setting('home.gallery_title', 'Tác phẩm nổi bật') }}
                </h2>
                <p class="text-gray-400 mt-4">
                    {{ setting('home.gallery_desc', 'Những kiểu tóc được thực hiện bởi đội ngũ stylist của chúng tôi') }}
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">


                @forelse ($galleries as $g)
                    <div class="relative aspect-square rounded-xl overflow-hidden group border border-gray-800">
                        <img src="{{ $g->image ? Voyager::image($g->image) : asset('images/service-default.jpg') }}"
                            alt="{{ $g->title }}" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-all"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/80 to-transparent">
                            <p class="text-sm font-medium text-white">{{ $g->title }}</p>
                            <p class="text-xs text-gray-400">{{ $g->subtitle }}</p>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>


    {{-- =========================
   CONTACT
========================= --}}
    <section id="contact" class="relative py-20 bg-darker">
        @php

            // Settings (Voyager)
            $address = setting('site.address', '123 Phan Đình Phùng, Thái Nguyên');
            $openHours = setting('site.open_hours', '8:00 - 22:00');

            $phoneRaw = setting('site.phone', '0399 869 844');
            $phoneTel = preg_replace('/\D+/', '', (string) $phoneRaw); // chỉ lấy số
            $phoneHref = $phoneTel ? '0' . ltrim($phoneTel, '0') : preg_replace('/\s+/', '', (string) $phoneRaw);

            $zaloLink = setting('site.zalo_link', $phoneTel ? 'https://zalo.me/' . $phoneTel : '#');
            $mapUrl = setting('site.map_url', '#'); // link Google Maps share
            $mapEmbed = setting('site.map_embed', ''); // dán iframe src (CHỈ src)

            // Simple open indicator (không parse giờ phức tạp)
            $isOpenNow = true;

            // Texts
            $contactTitle = setting('home.contact_title', 'Ghé thăm tiệm');
            $contactDesc = setting(
                'home.contact_desc',
                'Hãy đặt lịch trước để không phải chờ đợi. Liên hệ nhanh qua điện thoại hoặc Zalo để được hỗ trợ ngay.',
            );

            // Button labels (tuỳ chỉnh được)
            $btnCall = setting('home.contact_btn_call', 'Gọi ngay');
            $btnZalo = setting('home.contact_btn_zalo', 'Chat Zalo');
            $btnBook = setting('home.contact_btn_booking', 'Đặt lịch');
            $btnMap = setting('home.contact_btn_map', 'Chỉ đường');
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header center --}}
            <div class="text-center mb-16">
                <div class="text-gold text-xs font-semibold tracking-[0.2em] uppercase">Liên hệ</div>
                <h2 class="font-display text-4xl sm:text-5xl font-bold text-white mt-3">{{ $contactTitle }}</h2>
                <p class="text-gray-400 mt-3 max-w-2xl mx-auto">{{ $contactDesc }}</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-10 items-stretch">
                {{-- LEFT CARD --}}
                <div
                    class="h-full rounded-3xl border border-gold/25 bg-gradient-to-b from-[#151515] to-[#0b0b0b] p-8 shadow-[0_30px_80px_rgba(0,0,0,0.55)]">
                    {{-- Hotline block --}}
                    <div class="text-center">
                        <div
                            class="mx-auto w-12 h-12 rounded-2xl bg-gold/15 border border-gold/25 flex items-center justify-center text-gold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>

                        <div class="mt-4 text-white font-semibold">Hotline</div>
                        <div class="text-gray-400 text-sm mt-1">Luôn sẵn sàng phục vụ bạn</div>

                        <a href="tel:{{ $phoneHref }}"
                            class="mt-4 inline-flex items-center justify-center px-6 py-3 rounded-xl border border-gold/40 bg-black/25 text-gold font-bold text-xl tracking-wide hover:bg-gold hover:text-black transition-all">
                            {{ $phoneRaw }}
                        </a>


                    </div>

                    <div class="my-7 h-px bg-white/10"></div>

                    {{-- Address --}}
                    <div class="flex gap-4">
                        <div
                            class="shrink-0 w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 11a3 3 0 110-6 3 3 0 010 6z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11c0 7-7 11-7 11S5 18 5 11a7 7 0 1114 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Địa chỉ</div>
                            <div class="text-gray-400 text-sm mt-1">{{ $address }}</div>
                        </div>
                    </div>

                    {{-- Open hours --}}
                    <div class="flex gap-4 mt-5">
                        <div
                            class="shrink-0 w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Giờ mở cửa</div>
                            <div class="text-gold font-semibold mt-1">{{ $openHours }}</div>
                            <div class="mt-1 flex items-center gap-2 text-xs">
                                <span
                                    class="w-2 h-2 rounded-full {{ $isOpenNow ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                <span class="{{ $isOpenNow ? 'text-emerald-300' : 'text-red-300' }}">
                                    {{ $isOpenNow ? 'Đang mở cửa' : 'Đang đóng cửa' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="mt-7 grid grid-cols-2 gap-4">
                        <a href="tel:{{ $phoneHref }}"
                            class="rounded-xl border border-gold/35 text-gold font-semibold py-3 text-center hover:bg-gold hover:text-black transition-all">
                            {{ $btnCall }}
                        </a>

                        <a href="{{ $zaloLink }}" target="_blank" rel="noopener"
                            class="rounded-xl border border-blue-500/35 text-blue-200 font-semibold py-3 text-center hover:border-blue-400 hover:text-white transition-all">
                            {{ $btnZalo }}
                        </a>

                        <a href="{{ route('site.booking') }}"
                            class="col-span-1 rounded-xl gradient-gold text-black font-semibold py-3 text-center hover:opacity-90 transition-opacity">
                            {{ $btnBook }}
                        </a>

                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener"
                            class="col-span-1 rounded-xl border border-white/15 text-white font-semibold py-3 text-center hover:border-white/30 transition-all">
                            {{ $btnMap }}
                        </a>
                    </div>
                </div>

                {{-- RIGHT MAP --}}
                <div class="h-full rounded-3xl border border-white/10 overflow-hidden bg-white">
                    <div class="relative h-full min-h-[520px] lg:min-h-[560px]">
                        @if (!empty($mapEmbed))
                            <iframe src="{{ $mapEmbed }}" class="absolute inset-0 w-full h-full" style="border:0;"
                                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @else
                            {{-- Placeholder --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
