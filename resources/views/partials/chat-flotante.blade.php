{{-- Chat Flotante IA - Solo para gerente --}}
@auth
@if(auth()->user()->isGerente())
<div id="chat-container" class="fixed bottom-6 right-6 z-50">
    {{-- Botón flotante --}}
    <button id="chat-toggle"
            class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center text-white text-2xl hover:scale-110 focus:outline-none focus:ring-4 focus:ring-violet-300">
        <span id="chat-icon">🤖</span>
    </button>

    {{-- Chat flotante --}}
    <div id="chat-panel" class="hidden absolute bottom-20 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xl">🤖</span>
                <div>
                    <h3 class="font-bold text-sm">Asistente IA</h3>
                    <p class="text-xs opacity-80">Preguntas sobre ventas</p>
                </div>
            </div>
            <button id="chat-close" class="hover:bg-white/20 rounded-full p-1 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mensajes --}}
        <div id="chat-messages" class="h-72 overflow-y-auto p-4 space-y-3 bg-slate-50">
            {{-- Mensaje de bienvenida --}}
            <div class="flex gap-2">
                <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center text-sm">🤖</div>
                <div class="bg-white rounded-xl rounded-tl-none px-3 py-2 shadow-sm max-w-[80%]">
                    <p class="text-sm text-slate-700">¡Hola! Soy tu asistente. Pregúntame sobre ventas, inventario o tendencias.</p>
                </div>
            </div>
        </div>

        {{-- Sugerencias rápidas --}}
        <div id="chat-suggestions" class="px-3 py-2 bg-white border-t border-slate-100 flex flex-wrap gap-1">
            <button class="suggestion-btn text-xs bg-violet-50 text-violet-700 px-2 py-1 rounded-full hover:bg-violet-100 transition">Ventas de hoy</button>
            <button class="suggestion-btn text-xs bg-violet-50 text-violet-700 px-2 py-1 rounded-full hover:bg-violet-100 transition">Producto más vendido</button>
            <button class="suggestion-btn text-xs bg-violet-50 text-violet-700 px-2 py-1 rounded-full hover:bg-violet-100 transition">Stock bajo</button>
        </div>

        {{-- Input --}}
        <div class="p-3 bg-white border-t border-slate-200">
            <form id="chat-form" class="flex gap-2">
                @csrf
                <input type="text" 
                       id="chat-input" 
                       placeholder="Escribe tu pregunta..."
                       class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent"
                       autocomplete="off">
                <button type="submit" 
                        id="chat-send"
                        class="w-10 h-10 bg-violet-500 hover:bg-violet-600 text-white rounded-full flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    #chat-panel.show {
        opacity: 1;
        transform: scale(1);
    }
    #chat-messages::-webkit-scrollbar {
        width: 4px;
    }
    #chat-messages::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 2px;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 0.4; }
        50% { opacity: 1; }
    }
    .typing-dot {
        animation: pulse-dot 1.4s infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('chat-toggle');
    const panel = document.getElementById('chat-panel');
    const close = document.getElementById('chat-close');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    const icon = document.getElementById('chat-icon');
    const suggestions = document.querySelectorAll('.suggestion-btn');

    let isOpen = false;

    function toggleChat() {
        isOpen = !isOpen;
        panel.classList.toggle('hidden', !isOpen);
        setTimeout(() => panel.classList.toggle('show', isOpen), 10);
        icon.textContent = isOpen ? '✕' : '🤖';
        if (isOpen) input.focus();
    }

    toggle.addEventListener('click', toggleChat);
    close.addEventListener('click', toggleChat);

    // Cerrar con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) toggleChat();
    });

    // Sugerencias
    suggestions.forEach(btn => {
        btn.addEventListener('click', () => {
            input.value = btn.textContent;
            form.dispatchEvent(new Event('submit'));
        });
    });

    // Enviar mensaje
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const question = input.value.trim();
        if (!question) return;

        // Agregar mensaje del usuario
        addMessage(question, 'user');
        input.value = '';

        // Mostrar typing
        const typingId = showTyping();

        try {
            const response = await fetch('{{ route("panel.ia.ask") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ question })
            });

            const data = await response.json();
            removeTyping(typingId);

            if (data.success) {
                addMessage(data.response, 'bot');
            } else {
                addMessage('Lo siento, hubo un error. Intenta de nuevo.', 'bot');
            }
        } catch (error) {
            removeTyping(typingId);
            addMessage('Error de conexión. Verifica tu red.', 'bot');
        }
    });

    function addMessage(text, type) {
        const div = document.createElement('div');
        div.className = 'flex gap-2' + (type === 'user' ? ' justify-end' : '');
        
        if (type === 'user') {
            div.innerHTML = `
                <div class="bg-violet-500 text-white rounded-xl rounded-tr-none px-3 py-2 shadow-sm max-w-[80%]">
                    <p class="text-sm">${escapeHtml(text)}</p>
                </div>
                <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center text-sm">👤</div>
            `;
        } else {
            div.innerHTML = `
                <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center text-sm">🤖</div>
                <div class="bg-white rounded-xl rounded-tl-none px-3 py-2 shadow-sm max-w-[80%]">
                    <p class="text-sm text-slate-700">${formatResponse(text)}</p>
                </div>
            `;
        }
        
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    function showTyping() {
        const id = 'typing-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'flex gap-2';
        div.innerHTML = `
            <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center text-sm">🤖</div>
            <div class="bg-white rounded-xl rounded-tl-none px-4 py-3 shadow-sm flex gap-1">
                <span class="typing-dot w-2 h-2 bg-violet-400 rounded-full"></span>
                <span class="typing-dot w-2 h-2 bg-violet-400 rounded-full"></span>
                <span class="typing-dot w-2 h-2 bg-violet-400 rounded-full"></span>
            </div>
        `;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        return id;
    }

    function removeTyping(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatResponse(text) {
        // Convertir saltos de línea y formato básico
        return text
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }
});
</script>
@endif
@endauth
