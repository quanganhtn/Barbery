@extends('layouts.barbery')
@section('title', 'Phân tích khuôn mặt & gợi ý kiểu tóc')

@section('content')
    <section class="pt-32 lg:pt-36 pb-16 bg-darker min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {{-- Cột trái: upload ảnh --}}
                <div class="lg:col-span-4">
                    <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 lg:p-7 shadow-2xl">
                        <h2 class="text-2xl font-semibold text-white mb-2">Tải ảnh khuôn mặt</h2>
                        <p class="text-sm text-gray-400 mb-6 leading-7">
                            Ảnh nhìn thẳng, đủ sáng và rõ khuôn mặt sẽ cho kết quả tốt hơn.
                        </p>

                        <form id="ai-hairstyle-form" class="space-y-5">
                            @csrf

                            <label for="image"
                                class="block rounded-3xl border border-dashed border-white/40 bg-[#0d1118] hover:border-gold transition p-6 cursor-pointer">
                                <div class="flex flex-col items-center justify-center text-center gap-4">
                                    <div
                                        class="w-16 h-16 rounded-full gradient-gold text-black flex items-center justify-center shadow-lg">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>

                                    <div>
                                        <div class="text-white text-xl font-semibold">Chọn ảnh để phân tích</div>
                                        <div class="text-gray-400 text-sm mt-2">JPG, PNG, WEBP • tối đa 5MB</div>
                                    </div>
                                </div>

                                <input type="file" name="image" id="image" accept="image/*" class="hidden"
                                    required>
                            </label>

                            <div id="file-name"
                                class="hidden text-sm text-gold bg-gold/10 border border-white/20 rounded-2xl px-4 py-4 break-all">
                            </div>

                            <div id="image-preview-wrapper" class="hidden">
                                <div class="rounded-3xl overflow-hidden border border-white/10 bg-[#111318]">
                                    <img id="image-preview" src="" alt="Preview"
                                        class="w-full h-[360px] object-cover">
                                </div>
                            </div>

                            <button type="submit" id="submit-btn"
                                class="w-full gradient-gold text-black h-16 rounded-3xl text-xl font-semibold hover:opacity-90 transition-opacity pulse-gold shadow-lg">
                                Phân tích ngay
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Cột phải: kết quả --}}
                <div class="lg:col-span-8">
                    <div id="result-panel"
                        class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 lg:p-8 shadow-2xl">

                        {{-- Trạng thái rỗng --}}
                        <div id="empty-state"
                            class="rounded-3xl border border-dashed border-white/10 bg-[#0f1117] min-h-[420px] flex items-center justify-center text-center p-8">
                            <div>
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-gold">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 3a.75.75 0 00-.75.75v.443a8.968 8.968 0 00-4.915 2.04.75.75 0 00-.176.958l.278.482a.75.75 0 001.114.244 7.47 7.47 0 013.699-1.53v1.223a.75.75 0 001.5 0V6.371c.247-.015.496-.023.75-.023s.503.008.75.023V7.61a.75.75 0 001.5 0V6.387a7.47 7.47 0 013.699 1.53.75.75 0 001.114-.244l.278-.482a.75.75 0 00-.176-.958A8.968 8.968 0 0015 4.193V3.75A.75.75 0 0014.25 3h-4.5zM4.5 13.5a7.5 7.5 0 1115 0v1.125a2.625 2.625 0 01-2.625 2.625h-9.75A2.625 2.625 0 014.5 14.625V13.5z" />
                                    </svg>
                                </div>

                                <h3 class="text-2xl font-semibold text-white mb-3">Chưa có dữ liệu phân tích</h3>
                                <p class="text-gray-400 max-w-xl leading-7">
                                    Tải ảnh lên và nhấn “Phân tích ngay” để xem 5 tiêu chí khuôn mặt cùng 6 kiểu tóc phù hợp
                                    nhất.
                                </p>
                            </div>
                        </div>

                        {{-- Hộp lỗi --}}
                        <div id="error-box" class="hidden rounded-3xl border border-red-500/20 bg-red-500/10 p-5 mb-6">
                            <div class="text-red-300 text-base leading-7" id="error-text"></div>
                        </div>

                        {{-- Kết quả --}}
                        <div id="ai-result" class="hidden">
                            {{-- trạng thái chất lượng ảnh --}}
                            <div id="quality-box" class="hidden mb-6 rounded-3xl border border-white/10 bg-[#111318] p-5">
                                <div class="flex flex-wrap items-center gap-3 justify-between">
                                    <h3 class="text-xl font-semibold text-white">Trạng thái phân tích</h3>
                                    <span id="status-badge"
                                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold"></span>
                                </div>

                                <p id="result-message" class="text-gray-300 mt-3 leading-7"></p>

                                <div id="quality-issues-wrap" class="hidden mt-4">
                                    <div class="rounded-2xl border border-yellow-500/20 bg-yellow-500/10 p-4">
                                        <div class="text-yellow-300 font-medium mb-2">Lưu ý về ảnh</div>
                                        <ul id="quality-issues" class="list-disc pl-5 text-sm text-yellow-200 space-y-1">
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Tóm tắt gương mặt --}}
                            <div id="analysis-summary" class="mb-10">
                                <div class="mb-5">
                                    <h3 class="text-3xl font-semibold text-white">Tóm tắt gương mặt</h3>
                                    <p class="text-gray-400 mt-2 text-base leading-7">
                                        Hệ thống dùng 5 tiêu chí khuôn mặt để phân tích và đề xuất kiểu tóc phù hợp hơn.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
                                    <div class="rounded-3xl border border-white/10 bg-[#111318] p-5 min-h-[150px]">
                                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-3">
                                            1. Hình dạng khuôn mặt
                                        </div>
                                        <div id="summary-face-shape" class="text-gold text-xl font-medium leading-8">-</div>
                                    </div>

                                    <div class="rounded-3xl border border-white/10 bg-[#111318] p-5 min-h-[150px]">
                                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-3">
                                            2. Chiều dài khuôn mặt
                                        </div>
                                        <div id="summary-face-length" class="text-gold text-xl font-medium leading-8">-
                                        </div>
                                    </div>

                                    <div class="rounded-3xl border border-white/10 bg-[#111318] p-5 min-h-[150px]">
                                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-3">
                                            3. Chiều rộng khuôn mặt
                                        </div>
                                        <div id="summary-face-width" class="text-gold text-xl font-medium leading-8">-</div>
                                    </div>

                                    <div class="rounded-3xl border border-white/10 bg-[#111318] p-5 min-h-[150px]">
                                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-3">
                                            4. Kích thước trán
                                        </div>
                                        <div id="summary-forehead-size" class="text-gold text-xl font-medium leading-8">-
                                        </div>
                                    </div>

                                    <div class="rounded-3xl border border-white/10 bg-[#111318] p-5 min-h-[150px]">
                                        <div class="text-xs uppercase tracking-wider text-gray-500 mb-3">
                                            5. Đường hàm
                                        </div>
                                        <div id="summary-jawline-shape" class="text-gold text-xl font-medium leading-8">-
                                        </div>
                                    </div>
                                </div>

                                <div id="ai-note" class="mt-4 text-sm text-gray-400 leading-7"></div>
                            </div>

                            {{-- Nhóm dáng mặt hệ thống nghiêng về --}}
                            <div id="top-shapes-section" class="mb-10">
                                <div class="mb-5">
                                    <h3 class="text-2xl font-semibold text-white">Nhóm dáng mặt hệ thống nghiêng về</h3>
                                    <p class="text-gray-400 mt-2 text-base leading-6">
                                        Đây là các nhóm khuôn mặt có mức phù hợp cao nhất từ ảnh hiện tại.
                                    </p>
                                </div>

                                <div id="top-shapes-grid" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
                            </div>

                            {{-- 6 kiểu tóc --}}
                            <div>
                                <div class="mb-5">
                                    <h3 class="text-2xl font-semibold text-white">6 kiểu tóc đề xuất</h3>
                                    <p class="text-gray-400 mt-2 text-base leading-6">
                                        Gợi ý dựa trên 5 tiêu chí khuôn mặt, không chỉ dựa riêng vào dáng mặt.
                                    </p>
                                </div>

                                <div id="suggestions-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const imageInput = document.getElementById('image');
        const fileName = document.getElementById('file-name');
        const previewWrapper = document.getElementById('image-preview-wrapper');
        const previewImg = document.getElementById('image-preview');
        const emptyState = document.getElementById('empty-state');
        const aiResult = document.getElementById('ai-result');
        const resultPanel = document.getElementById('result-panel');
        const form = document.getElementById('ai-hairstyle-form');
        const submitBtn = document.getElementById('submit-btn');

        const errorBox = document.getElementById('error-box');
        const errorText = document.getElementById('error-text');

        const qualityBox = document.getElementById('quality-box');
        const statusBadge = document.getElementById('status-badge');
        const resultMessage = document.getElementById('result-message');
        const qualityIssuesWrap = document.getElementById('quality-issues-wrap');
        const qualityIssues = document.getElementById('quality-issues');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            fileName.classList.remove('hidden');
            fileName.textContent = `Đã chọn: ${file.name}`;

            const reader = new FileReader();
            reader.onload = function(ev) {
                previewWrapper.classList.remove('hidden');
                previewImg.src = ev.target.result;
            };
            reader.readAsDataURL(file);

            errorBox.classList.add('hidden');
        });

        function setStatusBadge(status) {
            statusBadge.className =
                'inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold';

            if (status === 'ok') {
                statusBadge.classList.add('bg-green-500/15', 'text-green-300', 'border', 'border-green-500/20');
                statusBadge.textContent = 'Ảnh tốt';
            } else if (status === 'low_quality_result') {
                statusBadge.classList.add('bg-yellow-500/15', 'text-yellow-300', 'border', 'border-yellow-500/20');
                statusBadge.textContent = 'Kết quả ước lượng';
            } else if (status === 'need_better_photo') {
                statusBadge.classList.add('bg-red-500/15', 'text-red-300', 'border', 'border-red-500/20');
                statusBadge.textContent = 'Cần chụp lại';
            } else {
                statusBadge.classList.add('bg-white/10', 'text-gray-300', 'border', 'border-white/10');
                statusBadge.textContent = 'Không xác định';
            }
        }

        function setSummary(data = {}) {
            document.getElementById('summary-face-shape').textContent =
                data.face_shape_label || 'Chưa xác định';

            document.getElementById('summary-face-length').textContent =
                data.metrics?.face_length || 'Chưa xác định';

            document.getElementById('summary-face-width').textContent =
                data.metrics?.face_width || 'Chưa xác định';

            document.getElementById('summary-forehead-size').textContent =
                data.metrics?.forehead_size || 'Chưa xác định';

            document.getElementById('summary-jawline-shape').textContent =
                data.metrics?.jawline_shape || 'Chưa xác định';

            const noteEl = document.getElementById('ai-note');
            const parts = [];

            if (typeof data.confidence === 'number') {
                parts.push(`Độ tin cậy: ${Math.round(data.confidence * 100)}%.`);
            }

            if (data.analysis_summary) {
                parts.push(data.analysis_summary);
            }

            if (Array.isArray(data.quality?.issues) && data.quality.issues.length) {
                parts.push(`Lưu ý: ${data.quality.issues.join('; ')}.`);
            } else if (data.quality?.message) {
                parts.push(data.quality.message);
            }

            noteEl.textContent = parts.join(' ');
        }

        function renderTopShapes(topShapes = []) {
            const grid = document.getElementById('top-shapes-grid');
            grid.innerHTML = '';

            if (!Array.isArray(topShapes) || topShapes.length === 0) {
                grid.innerHTML = `
                    <div class="rounded-3xl border border-white/10 bg-[#111318] p-5 text-gray-400">
                        Chưa có dữ liệu nhóm dáng mặt.
                    </div>
                `;
                return;
            }

            topShapes.forEach((item) => {
                const card = document.createElement('div');
                card.className = 'rounded-3xl border border-white/10 bg-[#111318] p-5';

                card.innerHTML = `
                    <div class="text-sm text-gray-400 mb-2">${item.label || 'Không xác định'}</div>
                    <div class="text-3xl text-gold font-semibold">${item.score || 0}%</div>
                `;

                grid.appendChild(card);
            });
        }

        function renderSuggestions(suggestions = []) {
            const grid = document.getElementById('suggestions-grid');
            grid.innerHTML = '';

            if (!Array.isArray(suggestions) || suggestions.length === 0) {
                grid.innerHTML = `
                    <div class="rounded-3xl border border-white/10 bg-[#111318] p-5 text-gray-400">
                        Chưa có kiểu tóc gợi ý từ ảnh hiện tại.
                    </div>
                `;
                return;
            }

            suggestions.forEach((item) => {
                const data = typeof item === 'string' ? {
                    name: item,
                    image: '',
                    description: 'Kiểu tóc phù hợp dựa trên phân tích AI.',
                } : item;

                const card = document.createElement('div');
                card.className =
                    'group rounded-3xl overflow-hidden border border-white/10 bg-[#111318] hover:border-gold/40 transition-all duration-300 hover:-translate-y-1 flex flex-col h-full';

                card.innerHTML = `
                    <div class="aspect-[4/5] overflow-hidden bg-black shrink-0">
                        ${data.image
                            ? `<img src="${data.image}" alt="${data.name}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">`
                            : `<div class="w-full h-full flex items-center justify-center text-gold text-sm">Không có ảnh minh họa</div>`
                        }
                    </div>

                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="text-white font-semibold text-lg leading-7 min-h-[56px]">${data.name || 'Kiểu tóc'}</h3>
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-gold text-black shrink-0">AI</span>
                        </div>

                        <p class="text-base text-gray-400 leading-8 min-h-[130px]">
                            ${data.description || ''}
                        </p>

                        <a href="{{ route('site.booking') }}"
                            class="mt-auto inline-flex items-center justify-center w-full h-12 rounded-2xl border border-gold text-gold hover:bg-gold hover:text-black transition-all text-lg font-semibold">
                            Chọn kiểu này
                        </a>
                    </div>
                `;

                grid.appendChild(card);
            });
        }

        function renderQuality(data = {}) {
            qualityBox.classList.remove('hidden');
            setStatusBadge(data.status || 'unknown');
            resultMessage.textContent = data.message || 'Đã hoàn tất phân tích.';

            qualityIssues.innerHTML = '';

            const issues = data.quality?.issues || [];
            if (Array.isArray(issues) && issues.length > 0) {
                qualityIssuesWrap.classList.remove('hidden');
                issues.forEach((issue) => {
                    const li = document.createElement('li');
                    li.textContent = issue;
                    qualityIssues.appendChild(li);
                });
            } else {
                qualityIssuesWrap.classList.add('hidden');
            }
        }

        function showError(message) {
            errorText.textContent = message || 'Có lỗi xảy ra.';
            errorBox.classList.remove('hidden');
            emptyState.classList.add('hidden');
            aiResult.classList.add('hidden');
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const selectedFile = imageInput.files[0];
            if (!selectedFile) {
                showError('Vui lòng chọn ảnh trước khi phân tích.');
                return;
            }

            errorBox.classList.add('hidden');

            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang phân tích...';

            try {
                const res = await fetch('/hairstyle/analyze', { //fetch('{{ route('hairstyle.analyze') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let json = {};
                try {
                    json = await res.json();
                } catch (e) {
                    throw new Error('Server không trả về JSON hợp lệ.');
                }

                if (!res.ok || json.status === 'error') {
                    throw new Error(json.message || 'Không phân tích được ảnh');
                }

                const data = json.data || {};

                if (json.status === 'need_better_photo') {
                    showError(data.message || 'Ảnh chưa đủ chuẩn để phân tích chính xác.');
                    return;
                }

                emptyState.classList.add('hidden');
                aiResult.classList.remove('hidden');

                renderQuality({
                    status: json.status,
                    message: json.message || data.message,
                    quality: data.quality || {}
                });

                setSummary(data);
                renderTopShapes(data.top_shapes || []);
                renderSuggestions(data.suggestions || []);

                setTimeout(() => {
                    resultPanel.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 150);
            } catch (err) {
                console.error(err);
                showError(err.message || 'Có lỗi khi gửi ảnh.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Phân tích ngay';
            }
        });
    </script>
@endsection
