
    const amigoId = @json($amigo->id); // Guardar el ID del amigo
    const userId = @json(session('user_id')); // Guardar el ID del usuario

    function cargarMensajes() {
        $.ajax({
            url: "{{ route('ver.chat', ['amigo_id' => $amigo->id]) }}",
            type: "GET",
            success: function(data) {
                const container = $('#messages-container');
                container.empty();
                data.mensajes.forEach(mensaje => {
                    const alignment = mensaje.usuario_id == userId ? 'justify-end' : 'justify-start';
                    const bgColor = mensaje.usuario_id == userId ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300';

                    container.append(`
                        <div class="flex ${alignment}">
                            <div class="${bgColor} rounded-lg px-4 py-2 max-w-[70%]">
                                <p class="text-sm">${mensaje.contenido}</p>
                                <p class="text-xs ${mensaje.usuario_id == userId ? 'text-blue-200' : 'text-gray-500'} mt-1">
                                    ${new Date(mensaje.fecha).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                </p>
                            </div>
                        </div>
                    `);
                });
                container.scrollTop(container[0].scrollHeight);
            },
            error: function(xhr) {
                console.error('Error al cargar mensajes:', xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        cargarMensajes();

        $('#message-form').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            if (!$('input[name="contenido"]').val()) {
                console.log('El mensaje no puede estar vacío.');
                return;
            }

            $.ajax({
                url: "{{ route('enviar.mensaje') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    cargarMensajes();
                    $('input[name="contenido"]').val('');
                },
                error: function(xhr) {
                    console.error('Error al enviar el mensaje:', xhr.responseText);
                }
            });
        });

        setInterval(cargarMensajes, 5000);
    });
