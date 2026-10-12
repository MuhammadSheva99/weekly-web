@php
    // Warna ditentukan dari awalan alamat halaman: /karyawan, /atasan, /hrd.
    // Bisa dipaksa lewat parameter: @include('components.ai-chat-modal', ['tema' => 'atasan'])
    $aiTemaList = [
        'karyawan' => ['main' => '#FBC02D', 'dark' => '#F9A825', 'text' => '#2B2200', 'accent' => '#A16207'],
        'atasan'   => ['main' => '#C90000', 'dark' => '#A30000', 'text' => '#FFFFFF', 'accent' => '#C90000'],
        'hrd'      => ['main' => '#2357BC', 'dark' => '#1B4596', 'text' => '#FFFFFF', 'accent' => '#2357BC'],
    ];
    $aiTema = $aiTemaList[$tema ?? request()->segment(1)] ?? $aiTemaList['karyawan'];
@endphp

<style>
    #btnAiChatModal, #aiChatModal {
        --aic: {{ $aiTema['main'] }};
        --aic-dark: {{ $aiTema['dark'] }};
        --aic-text: {{ $aiTema['text'] }};
        --aic-accent: {{ $aiTema['accent'] }};
    }
    .aic-bg { background: var(--aic); color: var(--aic-text); }
    .aic-btn { background: var(--aic); color: var(--aic-text); }
    .aic-btn:hover { background: var(--aic-dark); }
    .aic-bubble-user { background: var(--aic); color: var(--aic-text); }
    .aic-label-user { opacity: .8; }
    .aic-label-ai { color: var(--aic-accent); }
    .aic-spinner { border-color: var(--aic); border-top-color: transparent; }
    .aic-close { color: var(--aic-text); opacity: .85; }
    .aic-close:hover { opacity: 1; }
    .aic-input:focus { outline: none; border-color: var(--aic); box-shadow: 0 0 0 2px var(--aic); }
</style>

<!-- Tombol Floating AI Chat -->
<button type="button"
        id="btnAiChatModal"
        onclick="openAiChatModal('{{ $commitmentId ?? 1 }}')"
        class="aic-btn fixed bottom-6 right-6 rounded-full px-6 py-3.5 shadow-2xl z-[9999] flex items-center justify-center transition-all hover:scale-105 focus:outline-none cursor-pointer pointer-events-auto">
    <span class="font-semibold text-sm">Konsultasi Sekarang</span>
</button>

<!-- Modal AI Chat -->
<div id="aiChatModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col h-[550px]">
        <!-- Header -->
        <div class="aic-bg px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-lg flex items-center gap-2" id="aiChatTitle">
                AI Performance Coach
            </h3>
            <button type="button" onclick="closeAiChatModal()" class="aic-close text-2xl font-bold focus:outline-none">
                &times;
            </button>
        </div>

        <!-- Body / Chat Container -->
        <div id="chatMessagesContainer" class="p-4 overflow-y-auto flex-1 bg-gray-50 space-y-3">
            <div class="text-center text-gray-500 my-auto py-12" id="chatLoadingSpinner">
                <div class="aic-spinner inline-block animate-spin rounded-full h-8 w-8 border-4 mb-2"></div>
                <p class="text-sm">Menghubungkan ke AI Coach...</p>
            </div>
        </div>

        <!-- Footer / Form Input -->
        <div class="p-4 bg-white border-t border-gray-100">
            <form id="aiChatForm" onsubmit="sendAiMessage(event)" class="flex gap-2">
                <input type="hidden" id="currentChatId" value="">
                <input type="text" id="aiMessageInput"
                       class="aic-input flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm"
                       placeholder="Tulis pesan atau kendalamu..." required autocomplete="off">
                <button type="submit" id="btnSendAiMessage"
                        class="aic-btn px-5 py-2.5 rounded-lg text-sm font-semibold transition disabled:opacity-50">
                    Kirim
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let currentChatId = null;

// Mencegah teks yang mengandung tag HTML dijalankan sebagai HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

window.openAiChatModal = function(weeklyCommitmentId) {
    const modal = document.getElementById('aiChatModal');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('aiChatTitle').innerText = 'AI Performance Coach';
    document.getElementById('chatMessagesContainer').innerHTML = `
        <div class="text-center text-gray-500 my-auto py-12">
            <div class="aic-spinner inline-block animate-spin rounded-full h-8 w-8 border-4 mb-2"></div>
            <p class="text-sm">Menghubungkan ke AI Coach...</p>
        </div>
    `;

    fetch(`/ai-chat/init/${weeklyCommitmentId}`)
        .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok || !data || data.error) {
                const errorMsg = (data && (data.error || data.message)) ? (data.error || data.message) : `HTTP Error ${res.status}`;
                throw new Error(errorMsg);
            }
            return data;
        })
        .then(data => {
            currentChatId = data.chat_id;
            document.getElementById('currentChatId').value = data.chat_id;
            document.getElementById('aiChatTitle').innerText = data.title || 'AI Performance Coach';
            renderMessages(data.messages || []);
        })
        .catch(err => {
            console.error('AI Chat Error:', err);
            document.getElementById('chatMessagesContainer').innerHTML = `
                <div class="text-center my-auto py-8 px-4">
                    <p class="text-red-500 font-semibold text-sm mb-1">Gagal memuat AI Chat</p>
                    <p class="text-xs text-red-600 bg-red-50 p-3 rounded-lg border border-red-100 max-w-md mx-auto font-mono text-left overflow-x-auto">${escapeHtml(err.message)}</p>
                </div>
            `;
        });
};

window.closeAiChatModal = function() {
    const modal = document.getElementById('aiChatModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

function renderMessages(messages) {
    const container = document.getElementById('chatMessagesContainer');
    container.innerHTML = '';

    messages.forEach(msg => {
        const isUser = msg.sender === 'user';
        const msgHtml = `
            <div class="flex ${isUser ? 'justify-end' : 'justify-start'}">
                <div class="p-3.5 rounded-2xl text-sm ${isUser ? 'aic-bubble-user rounded-br-none' : 'bg-white text-gray-800 shadow-sm border border-gray-100 rounded-bl-none'} max-w-[80%] whitespace-pre-line">
                    <span class="font-semibold block text-xs mb-1 ${isUser ? 'aic-label-user' : 'aic-label-ai'}">${isUser ? 'Kamu' : 'AI Coach'}</span>
                    ${escapeHtml(msg.message)}
                </div>
            </div>
        `;
        container.innerHTML += msgHtml;
    });

    container.scrollTop = container.scrollHeight;
}

function sendAiMessage(e) {
    e.preventDefault();
    const input = document.getElementById('aiMessageInput');
    const message = input.value.trim();
    if (!message || !currentChatId) return;

    const btn = document.getElementById('btnSendAiMessage');
    input.value = '';
    btn.disabled = true;

    const container = document.getElementById('chatMessagesContainer');
    container.innerHTML += `
        <div class="flex justify-end">
            <div class="p-3.5 rounded-2xl text-sm aic-bubble-user rounded-br-none max-w-[80%] whitespace-pre-line">
                <span class="font-semibold block text-xs mb-1 aic-label-user">Kamu</span>
                ${escapeHtml(message)}
            </div>
        </div>
        <div id="typingIndicator" class="flex justify-start">
            <div class="p-3.5 rounded-2xl text-sm bg-white text-gray-400 shadow-sm border border-gray-100 rounded-bl-none">
                <em class="animate-pulse">AI Coach sedang mengetik...</em>
            </div>
        </div>
    `;
    container.scrollTop = container.scrollHeight;

    fetch('/ai-chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            chat_id: currentChatId,
            message: message
        })
    })
    .then(async res => {
        const data = await res.json().catch(() => null);
        if (!res.ok || !data || data.error) {
            throw new Error((data && data.error) ? data.error : `HTTP Error ${res.status}`);
        }
        return data;
    })
    .then(data => {
        document.getElementById('typingIndicator')?.remove();
        btn.disabled = false;

        container.innerHTML += `
            <div class="flex justify-start">
                <div class="p-3.5 rounded-2xl text-sm bg-white text-gray-800 shadow-sm border border-gray-100 rounded-bl-none max-w-[80%] whitespace-pre-line">
                    <span class="font-semibold block text-xs mb-1 aic-label-ai">AI Coach</span>
                    ${escapeHtml(data.ai_message.message)}
                </div>
            </div>
        `;
        container.scrollTop = container.scrollHeight;
    })
    .catch(err => {
        document.getElementById('typingIndicator')?.remove();
        btn.disabled = false;

        container.innerHTML += `
            <div class="flex justify-start">
                <div class="p-3 rounded-xl text-xs bg-red-50 text-red-600 border border-red-100 max-w-[80%] font-mono">
                    ⚠️ ${escapeHtml(err.message)}
                </div>
            </div>
        `;
        container.scrollTop = container.scrollHeight;
    });
}
</script>