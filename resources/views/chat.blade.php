@extends('base')

@section('title', 'Chat con ' . $amigo->nombres)

@section('content')
<div class="h-[calc(100vh-9rem)] flex flex-col space-y-4">
    <div class="flex-1 bg-gray-800/50 backdrop-blur-md rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
        <div class="flex flex-col h-full">
            <!-- Header del chat -->
            <div class="border-b border-gray-700/50 px-6 py-4 backdrop-blur-md bg-gray-800/50">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @php
                            $defaultPath = '/img/default-avatar.png';
                            $fotoPath = $amigo->foto_perfil 
                                ? '/storage/fotos_perfil/' . $amigo->foto_perfil 
                                : $defaultPath;
                        @endphp

                        <a href="{{ route('ver.perfil', $amigo->id) }}">
                            <img 
                                src="{{ $fotoPath }}" 
                                alt="Foto de {{ $amigo->nombres }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-gray-600"
                                onerror="this.src='{{ $defaultPath }}'"
                            >
                        </a>
                    </div>
                    <div>
                        <h2 class="font-semibold text-lg">{{ $amigo->nombres }}</h2>
                        <div class="flex items-center space-x-2 text-gray-400 text-sm">
                            <span>ID: {{ $amigo->id }}</span>
                            <span>•</span>
                            <span>{{ $amigo->correo }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenedor de mensajes -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-container">
                @foreach($mensajes as $mensaje)
                    <div class="flex {{ $mensaje->usuario_id == session('user_id') ? 'justify-end' : 'justify-start' }}">
                        <div class="flex items-end space-x-2 max-w-[70%] group">
                            @if($mensaje->usuario_id != session('user_id'))
                                <div class="h-6 w-6 rounded-full bg-gradient-to-r from-cyan-500 to-blue-500 flex-shrink-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <span class="text-xs font-semibold text-white">{{ substr($amigo->nombres, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="{{ $mensaje->usuario_id == session('user_id') 
                                ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white' 
                                : 'bg-gray-700/50 text-gray-200' }} 
                                rounded-2xl px-4 py-2 shadow-lg">
                                <p class="text-sm">{{ $mensaje->contenido }}</p>
                                <p class="text-xs {{ $mensaje->usuario_id == session('user_id') 
                                    ? 'text-blue-100' 
                                    : 'text-gray-400' }} mt-1">
                                    {{ \Carbon\Carbon::parse($mensaje->fecha)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Formulario de envío -->
            <div class="border-t border-gray-700/50 p-4 bg-gray-800/30 backdrop-blur-md">
                <form id="message-form" class="flex items-center space-x-3">
                    @csrf
                    <input type="hidden" name="amigo_id" value="{{ $amigo->id }}">
                    <div class="flex-1 relative">
                        <input type="text" 
                            name="contenido" 
                            required 
                            class="w-full rounded-full border border-gray-600 bg-gray-700/50 text-white px-4 py-2.5 pr-12 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all duration-200"
                            placeholder="Escribe un mensaje...">
                        <button type="submit" 
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full text-white hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const userId = @json(session('user_id'));
    const amigoId = @json($amigo->id);
    const lastMsgId = @json(count($mensajes) ? end($mensajes)->id : 0);
    let lastMessageId = lastMsgId;
    let isCheckingMessages = false;
    let messageContainer = $('#messages-container');
    let pendingMessages = new Set();

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    }

    function getCurrentTime() {
        return moment().format('HH:mm');
    }

    function checkNewMessages() {
        if (isCheckingMessages) return;

        isCheckingMessages = true;
        $.ajax({
            url: `/chat/${amigoId}/check-new`,
            method: 'GET',
            data: { last_id: lastMessageId },
            success: function(response) {
                if (response.mensajes && response.mensajes.length > 0) {
                    response.mensajes.forEach(function(mensaje) {
                        const tempId = `temp_${mensaje.contenido}_${userId}`;
                        const tempElement = $(`#${tempId}`);

                        if (tempElement.length && mensaje.usuario_id === userId) {
                            tempElement.attr('id', `message-${mensaje.id}`);
                            pendingMessages.delete(tempId);
                        } 
                        else if (!$(`#message-${mensaje.id}`).length && !pendingMessages.has(tempId)) {
                            const messageHtml = `
                                <div id="message-${mensaje.id}" class="flex ${mensaje.usuario_id === userId ? 'justify-end' : 'justify-start'}">
                                    <div class="flex items-end space-x-2 max-w-[70%] group">
                                        ${mensaje.usuario_id !== userId ? `
                                            <div class="h-6 w-6 rounded-full bg-gradient-to-r from-cyan-500 to-blue-500 flex-shrink-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <span class="text-xs font-semibold text-white">${mensaje.usuario_id !== userId ? '@json(substr($amigo->nombres, 0, 1))' : ''}</span>
                                            </div>
                                        ` : ''}
                                        <div class="${mensaje.usuario_id === userId 
                                            ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white' 
                                            : 'bg-gray-700/50 text-gray-200'} 
                                            rounded-2xl px-4 py-2 shadow-lg">
                                            <p class="text-sm">${mensaje.contenido}</p>
                                            <p class="text-xs ${mensaje.usuario_id === userId 
                                                ? 'text-blue-100' 
                                                : 'text-gray-400'} mt-1">
                                                ${mensaje.fecha}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            messageContainer.append(messageHtml);
                        }
                        
                        lastMessageId = Math.max(lastMessageId, mensaje.id);
                    });
                    scrollToBottom();
                }
            },
            complete: function() {
                isCheckingMessages = false;
            }
        });
    }

    $(document).ready(function() {
        scrollToBottom();
        setInterval(checkNewMessages, 3000);

        $('#message-form').on('submit', function(e) {
            e.preventDefault();

            const mensaje = $('input[name="contenido"]').val().trim();
            if (!mensaje) return;

            const formData = $(this).serialize();
            $('input[name="contenido"]').val('');

            const tempId = `temp_${mensaje}_${userId}`;
            pendingMessages.add(tempId);
            
            const tempMessageHtml = `
                <div id="${tempId}" class="flex justify-end">
                    <div class="flex items-end space-x-2 max-w-[70%]">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-2xl px-4 py-2 shadow-lg">
                            <p class="text-sm">${mensaje}</p>
                            <p class="text-xs text-blue-100 mt-1">${getCurrentTime()}</p>
                        </div>
                    </div>
                </div>
            `;
            messageContainer.append(tempMessageHtml);
            scrollToBottom();

            $.ajax({
                url: "{{ route('enviar.mensaje') }}",
                method: "POST",
                data: formData,
                error: function(xhr) {
                    console.error('Error al enviar el mensaje:', xhr.responseText);
                    $(`#${tempId}`).remove();
                    pendingMessages.delete(tempId);
                }
            });
        });
    });
})();
</script>
@endsection