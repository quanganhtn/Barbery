<div id="floating-chat" class="fixed bottom-5 right-5 z-[60]">
    {{-- Nút chat nổi --}}
    <button id="chat-toggle"
        class="group flex items-center gap-3 rounded-full border border-gold/30 bg-darker/95 backdrop-blur-md shadow-2xl px-4 py-3 text-white hover:border-gold hover:shadow-[0_0_30px_rgba(212,175,55,0.25)] transition-all">
        <div class="w-12 h-12 rounded-full gradient-gold flex items-center justify-center text-black shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z" />
            </svg>
        </div>

        <div class="hidden sm:block text-left leading-tight">
            <div class="text-sm font-semibold text-gold">Barbery</div>
            <div class="text-xs text-gray-400">Tư vấn nhanh</div>
        </div>
    </button>

    {{-- Khung chat --}}
    <div id="chat-panel"
        class="hidden opacity-0 translate-y-3 pointer-events-none absolute bottom-20 right-0 w-[340px] sm:w-[380px] rounded-3xl border border-white/10 bg-[#0b1220]/95 backdrop-blur-xl shadow-2xl overflow-hidden transition-all duration-300">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
            <div>
                <div class="font-semibold text-white">Barbery Assistant</div>
                <div class="text-xs text-gray-400">Tư vấn kiểu tóc và dịch vụ</div>
            </div>

            <button id="chat-close" class="text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="chat-box" class="h-80 overflow-y-auto px-4 py-4 space-y-3 text-sm bg-transparent">
            <div class="flex justify-start">
                <div class="max-w-[85%] rounded-2xl rounded-bl-md bg-white/10 text-gray-200 px-4 py-3">
                    Xin chào, tôi có thể giúp bạn tư vấn kiểu tóc, dịch vụ hoặc đặt lịch.
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-white/10 bg-[#0b1220]">

            <div id="chat-suggestions" class="mb-3 space-y-2">
                <button type="button"
                    class="chat-suggestion w-full text-left rounded-xl bg-white/10 hover:bg-white/15 text-gray-200 px-4 py-2 text-sm transition">
                    Barbery có những dịch vụ nào?
                </button>

                <button type="button"
                    class="chat-suggestion w-full text-left rounded-xl bg-white/10 hover:bg-white/15 text-gray-200 px-4 py-2 text-sm transition">
                    Tôi muốn đặt lịch cắt tóc
                </button>

                <button type="button"
                    class="chat-suggestion w-full text-left rounded-xl bg-white/10 hover:bg-white/15 text-gray-200 px-4 py-2 text-sm transition">
                    Làm sao để tra cứu lịch hẹn?
                </button>
            </div>

            <div class="flex gap-2">
                <input id="chat-input" type="text"
                    class="flex-1 rounded-2xl border border-white/10 bg-white/5 text-white px-4 py-3 outline-none focus:border-gold placeholder:text-gray-500"
                    placeholder="Nhập câu hỏi...">

                <button id="chat-send"
                    class="rounded-2xl gradient-gold text-black px-5 py-3 font-semibold hover:opacity-90 transition">
                    Gửi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const toggleBtn = document.getElementById('chat-toggle');
        const closeBtn = document.getElementById('chat-close');
        const panel = document.getElementById('chat-panel');
        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('chat-send');
        const box = document.getElementById('chat-box');
        const suggestions = document.querySelectorAll('.chat-suggestion');

        function escapeHtml(str) {
            return String(str).replace(/[&<>"']/g, function(m) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                })[m];
            });
        }

        function openChat() {
            panel.classList.remove('hidden', 'opacity-0', 'translate-y-3', 'pointer-events-none');
            panel.classList.add('opacity-100', 'translate-y-0');
            setTimeout(() => input.focus(), 100);
        }

        function closeChat() {
            panel.classList.add('opacity-0', 'translate-y-3', 'pointer-events-none');
            panel.classList.remove('opacity-100', 'translate-y-0');
            setTimeout(() => panel.classList.add('hidden'), 300);
        }

        function toggleChat() {
            if (panel.classList.contains('hidden')) {
                openChat();
            } else {
                closeChat();
            }
        }

        function appendUserMessage(message) {
            box.innerHTML += `
                <div class="flex justify-end">
                    <div class="max-w-[85%] rounded-2xl rounded-br-md bg-[#D4AF37] text-black px-4 py-3 font-medium shadow-lg break-words">
                        ${escapeHtml(message)}
                    </div>
                </div>
            `;
            box.scrollTop = box.scrollHeight;
        }

        function appendBotMessage(message) {
            box.innerHTML += `<div class = "flex justify-start" >
                    <div class =
                    "max-w-[85%] rounded-2xl rounded-bl-md bg-white/10 text-gray-200 px-4 py-3 break-words whitespace-pre-line" >
                    ${escapeHtml(message)}
                    </div>
                </div>
                `;
            box.scrollTop = box.scrollHeight;
        }

        function appendErrorMessage(message) {
            box.innerHTML += ` <div class = "flex justify-start" >
                    <div class =
                    "max-w-[85%] rounded-2xl rounded-bl-md bg-red-500/20 text-red-200 px-4 py-3 break-words" >
                    ${escapeHtml(message)}
                    </div>
                </div>
                `;
            box.scrollTop = box.scrollHeight;
        }

        function appendLoading() {
            box.innerHTML += `
            <div id = "chat-loading"
                class = "flex justify-start">
                <div class = "max-w-[85%] rounded-2xl rounded-bl-md bg-white/10 text-gray-300 px-4 py-3" >
                Đang trả lời...
                    </div>
                    </div>
                    `;
            box.scrollTop = box.scrollHeight;
        }

        function removeLoading() {
            document.getElementById('chat-loading')?.remove();
        }

        async function sendMessage() {
            const message = input.value.trim();
            if (!message) return;

            appendUserMessage(message);

            input.value = '';
            input.disabled = true;
            sendBtn.disabled = true;

            appendLoading();

            try {
                const res = await fetch('/chatbot/send', { //fetch('{{ route('chatbot.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message
                    })
                });

                const json = await res.json().catch(() => ({}));

                removeLoading();

                if (!res.ok) {
                    appendErrorMessage(json.message || 'Có lỗi khi gửi tin nhắn. Vui lòng thử lại.');
                    return;
                }

                appendBotMessage(json.reply || 'Không có phản hồi');
            } catch (error) {
                removeLoading();
                appendErrorMessage('Có lỗi khi gửi tin nhắn. Vui lòng thử lại.');
            } finally {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            }
        }

        toggleBtn.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', closeChat);
        sendBtn.addEventListener('click', sendMessage);
        suggestions.forEach(function(btn) {
            btn.addEventListener('click', function() {
                input.value = btn.textContent.trim();
                sendMessage();
            });
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
    })();
</script>
