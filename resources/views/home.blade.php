@extends('base')

@section('title', 'Inicio')

@section('content')
    <div class="max-w-2xl mx-auto">
        @if (session('success'))
            <div id="success-message" class="bg-gray-700 border border-gray-600 text-gray-300 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <form action="{{ route('crear.publicacion') }}" method="POST" enctype="multipart/form-data" id="publicacionForm">
                @csrf
                <textarea name="contenido" rows="3"
                    class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-4 focus:outline-none focus:border-blue-500"
                    placeholder="¿Qué estás pensando?" id="contenidoPublicacion"></textarea>

                <!-- Previsualización de imágenes -->
                <div id="imagePreview" class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                </div>

                <div class="mt-4 flex justify-between items-center">
                    <div class="flex space-x-4">
                        <!-- Botón para subir imágenes -->
                        <label class="cursor-pointer">
                            <input type="file" multiple accept="image/*" class="hidden" id="imageInput"
                                name="imagenes[]">
                            <span class="flex items-center text-blue-500 hover:text-blue-400">
                                <i class="fas fa-images mr-2"></i>
                                Imágenes
                            </span>
                        </label>
                    </div>
                    <button type="submit"
                        class="px-5 py-2 border-2 border-blue-500 text-blue-500 rounded-full hover:bg-blue-500 hover:text-white focus:outline-none transition-all duration-300 transform hover:scale-105">
                        Publicar
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de publicaciones -->
        <div class="space-y-6">
            @forelse($publicaciones as $publicacion)
                <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex items-start space-x-3">
                            <!-- Foto de perfil -->
                            <img src="{{ asset('storage/fotos_perfil/' . $publicacion->foto_perfil) }}"
                                alt="{{ $publicacion->nombres }}" class="w-11 h-11 rounded-full">

                            <!-- Información del usuario y fecha -->
                            <div>
                                <a href="{{ route('ver.perfil', $publicacion->usuario_id) }}"
                                    class="font-semibold text-cyan-400 hover:underline">
                                    {{ $publicacion->nombres }}
                                </a>
                                <!-- Fecha debajo del nombre -->
                                <p class="text-sm text-gray-400 fecha-publicacion"
                                    data-timestamp="{{ $publicacion->created_at }}"></p>
                            </div>
                        </div>
                        <!-- Mostrar opciones de editar/eliminar si es la publicación del usuario actual -->
                        @if ($publicacion->usuario_id == session('user_id'))
                            <div class="ml-auto">
                                <!-- Botón para mostrar el formulario de edición en línea -->
                                <button
                                    onclick="document.getElementById('edit-form-{{ $publicacion->id }}').classList.toggle('hidden')"
                                    class="font-semibold bg-green-500 text-white p-2 rounded-lg hover:bg-green-600 focus:outline-none">
                                    <!-- Ícono de editar (lapiz) -->
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Formulario de edición en línea -->
                                <form id="edit-form-{{ $publicacion->id }}"
                                    action="{{ route('editar.publicacion', $publicacion->id) }}" method="POST"
                                    class="hidden mt-4">
                                    @csrf
                                    <textarea name="contenido" rows="2" class="w-full bg-gray-700 text-white rounded-lg p-2 mb-2">{{ $publicacion->contenido }}</textarea>
                                    <button type="submit"
                                        class="font-semibold bg-orange-500 text-white p-2 rounded-lg hover:bg-orange-600 focus:outline-none mb-2">
                                        <!-- Ícono de guardar (disquete) -->
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                </form>

                                <!-- Botón para eliminar -->
                                <form action="{{ route('eliminar.publicacion', $publicacion->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="font-semibold bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 focus:outline-none"
                                        onclick="return confirm('¿Estás seguro de que quieres eliminar esta publicación?')">
                                        <!-- Ícono de eliminar (papelera) -->
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <p class="text-gray-200 whitespace-pre-wrap">{{ $publicacion->contenido }}</p>

                    <!-- Grid de imágenes -->
                    @if ($publicacion->imagenes)
                        @php
                            $imagenes = explode('|', $publicacion->imagenes);
                            $count = count($imagenes);
                        @endphp

                        <div class="mt-4">
                            @if ($count === 1)
                                <!-- Una sola imagen -->
                                <div class="w-full rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $imagenes[0]) }}"
                                        class="w-full h-auto cursor-pointer hover:opacity-90"
                                        onclick="openImageViewer({{ json_encode($imagenes) }}, 0)"
                                        alt="Imagen de la publicación">
                                </div>
                            @elseif($count === 2)
                                <!-- Dos imágenes -->
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach ($imagenes as $index => $imagen)
                                        <div class="rounded-lg overflow-hidden">
                                            <img src="{{ asset('storage/' . $imagen) }}"
                                                class="w-full h-48 object-cover cursor-pointer hover:opacity-90"
                                                onclick="openImageViewer({{ json_encode($imagenes) }}, {{ $index }})"
                                                alt="Imagen de la publicación">
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($count === 3)
                                <!-- Tres imágenes -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/' . $imagenes[0]) }}"
                                            class="w-full h-96 object-cover cursor-pointer hover:opacity-90"
                                            onclick="openImageViewer({{ json_encode($imagenes) }}, 0)"
                                            alt="Imagen de la publicación">
                                    </div>
                                    <div class="grid grid-rows-2 gap-2">
                                        @foreach (array_slice($imagenes, 1, 2) as $index => $imagen)
                                            <div class="rounded-lg overflow-hidden">
                                                <img src="{{ asset('storage/' . $imagen) }}"
                                                    class="w-full h-[11.75rem] object-cover cursor-pointer hover:opacity-90"
                                                    onclick="openImageViewer({{ json_encode($imagenes) }}, {{ $index + 1 }})"
                                                    alt="Imagen de la publicación">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- Cuatro o más imágenes -->
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach (array_slice($imagenes, 0, 4) as $index => $imagen)
                                        <div class="rounded-lg overflow-hidden relative">
                                            <img src="{{ asset('storage/' . $imagen) }}"
                                                class="w-full h-48 object-cover cursor-pointer hover:opacity-90"
                                                onclick="openImageViewer({{ json_encode($imagenes) }}, {{ $index }})"
                                                alt="Imagen de la publicación">
                                            @if ($index === 3 && $count > 4)
                                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center cursor-pointer"
                                                    onclick="openImageViewer({{ json_encode($imagenes) }}, {{ $index }})">
                                                    <span class="text-white text-2xl font-bold">+{{ $count - 4 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Sección de likes y comentarios -->
                    <div class="mt-4 flex items-center space-x-4">
                        <!-- Botón de like -->
                        <button class="like-button flex items-center space-x-2"
                            data-publicacion-id="{{ $publicacion->id }}" data-liked="{{ $publicacion->user_liked }}">
                            <i class="text-red-500 {{ $publicacion->user_liked ? 'fas' : 'far' }} fa-heart"></i>
                            <span class="text-sm like-count">{{ $publicacion->likes_count }}</span>
                        </button>

                        <!-- Botón para mostrar/ocultar comentarios -->
                        <button class="toggle-comments flex items-center space-x-2"
                            data-publicacion-id="{{ $publicacion->id }}">
                            <i class="far fa-comment text-blue-500"></i>
                            <span class="text-sm">{{ $publicacion->comentarios_count }}</span>
                        </button>
                    </div>

                    <!-- Sección de comentarios (inicialmente oculta) -->
                    <div id="comentarios-{{ $publicacion->id }}" class="mt-4 hidden">
                        <!-- Formulario para nuevo comentario -->
                        <form class="comentar-form mb-4">
                            <div class="flex space-x-2">
                                <input type="text"
                                    class="flex-1 bg-gray-700 text-white rounded-lg border border-gray-600 px-4 py-2"
                                    placeholder="Escribe un comentario...">
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                    Comentar
                                </button>
                            </div>
                        </form>

                        <!-- Lista de comentarios -->
                        <div class="comentarios-lista space-y-2">
                            <!-- Los comentarios se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                    <p class="text-gray-400">No hay publicaciones para mostrar.</p>
                    <p class="text-sm text-gray-500 mt-2">
                        ¡Sé el primero en publicar algo o agrega más amigos para ver sus publicaciones!
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // JavaScript para ocultar el mensaje de éxito después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success-message');

            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 4000);
            }
        });

        $(document).ready(function() {
            // Manejo de likes (ya existente, actualizado)
            $('.like-button').click(function(e) {
                e.preventDefault();
                var button = $(this);
                var publicacionId = button.data('publicacion-id');
                var liked = button.data('liked');
                var countElement = button.find('.like-count');

                $.ajax({
                    url: `/publicacion/${publicacionId}/like`,
                    method: liked ? 'DELETE' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        button.data('liked', !liked);
                        button.find('i').toggleClass('fas far');
                        var currentCount = parseInt(countElement.text());
                        countElement.text(liked ? currentCount - 1 : currentCount + 1);
                    }
                });
            });

            // Manejo de comentarios
            $('.toggle-comments').click(function() {
                var publicacionId = $(this).data('publicacion-id');
                var comentariosDiv = $(`#comentarios-${publicacionId}`);
                var toggleButton = $(this);

                if (comentariosDiv.is(':hidden')) {
                    comentariosDiv.find('.comentarios-lista').html(
                        '<div class="text-center text-gray-400">Cargando comentarios...</div>');
                    comentariosDiv.removeClass('hidden');

                    $.ajax({
                        url: `/publicacion/${publicacionId}/comentarios`,
                        method: 'GET',
                        success: function(comentarios) {
                            if (!Array.isArray(comentarios)) {
                                console.error('La respuesta no es un array:', comentarios);
                                comentariosDiv.find('.comentarios-lista').html(
                                    '<div class="text-center text-red-400">Error al cargar los comentarios</div>'
                                );
                                return;
                            }

                            toggleButton.find('span').text(comentarios.length);

                            if (comentarios.length === 0) {
                                comentariosDiv.find('.comentarios-lista').html(
                                    '<div class="text-center text-gray-400">No hay comentarios aún</div>'
                                );
                                return;
                            }

                            var comentariosHtml = comentarios.map(function(c) {
                                const esAutor = parseInt(c.usuario_id) === parseInt(
                                    {{ session('user_id') }});
                                return `
                                <div class="bg-gray-700 rounded-lg p-3" id="comentario-${c.id}" data-publicacion-id="${publicacionId}">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-cyan-400">${c.nombres}</span>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-400">${moment(c.created_at).fromNow()}</span>
                                            ${esAutor ? `
                                                            <button onclick="mostrarEditarComentario(${c.id})" 
                                                                    class="text-xs text-green-500 hover:text-green-400 ml-2">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button onclick="eliminarComentario(${c.id})" 
                                                                    class="text-xs text-red-500 hover:text-red-400">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        ` : ''}
                                        </div>
                                    </div>
                                    <p class="text-sm mt-1 comentario-contenido">${c.contenido}</p>
                                    <form class="editar-comentario-form hidden mt-2">
                                        <div class="flex space-x-2">
                                            <input type="text" 
                                                class="flex-1 bg-gray-600 text-white rounded-lg border border-gray-500 px-3 py-1 text-sm"
                                                value="${c.contenido}">
                                            <button type="submit" onclick="editarComentario(event, ${c.id})" 
                                                    class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 text-sm">
                                                Guardar
                                            </button>
                                            <button type="button" onclick="cancelarEditarComentario(${c.id})"
                                                    class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 text-sm">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                    <div class="mt-2 flex items-center">
                                        <button class="comentario-like-button flex items-center space-x-2" 
                                                data-comentario-id="${c.id}"
                                                data-liked="${c.user_liked ? 'true' : 'false'}">
                                            <i class="text-red-500 ${c.user_liked ? 'fas' : 'far'} fa-heart"></i>
                                            <span class="text-xs comentario-like-count">${c.likes_count}</span>
                                        </button>
                                    </div>
                                </div>
                            `;
                            }).join('');

                            comentariosDiv.find('.comentarios-lista').html(comentariosHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al cargar comentarios:', error);
                            comentariosDiv.find('.comentarios-lista').html(
                                '<div class="text-center text-red-400">Error al cargar los comentarios</div>'
                            );
                        }
                    });
                } else {
                    comentariosDiv.addClass('hidden');
                }
            });

            // Agregar el manejo de likes en comentarios
            $(document).on('click', '.comentario-like-button', function(e) {
                e.preventDefault();
                var button = $(this);
                var comentarioId = button.data('comentario-id');
                var liked = button.data('liked');
                var countElement = button.find('.comentario-like-count');

                $.ajax({
                    url: `/comentario/${comentarioId}/like`,
                    method: liked ? 'DELETE' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        button.data('liked', !liked);
                        button.find('i').toggleClass('fas far');
                        var currentCount = parseInt(countElement.text());
                        countElement.text(liked ? currentCount - 1 : currentCount + 1);
                    }
                });
            });

            // Manejo del formulario de comentarios
            $('.comentar-form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var publicacionContainer = form.closest('.bg-gray-800');
                var publicacionId = publicacionContainer.find('.toggle-comments').data('publicacion-id');
                var input = form.find('input');
                var contenido = input.val();

                if (!contenido.trim()) return;

                $.ajax({
                    url: `/publicacion/${publicacionId}/comentar`,
                    method: 'POST',
                    data: {
                        contenido: contenido,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Agregar el nuevo comentario al principio de la lista
                        var nuevoComentario = `
                        <div class="bg-gray-700 rounded-lg p-3" id="comentario-${response.comentario.id}" data-publicacion-id="${publicacionId}">
                            <div class="flex justify-between">
                                <span class="font-medium text-cyan-400">${response.comentario.nombres}</span>
                                <div class="flex space-x-2">
                                    <span class="text-xs text-gray-400">ahora</span>
                                    <button onclick="mostrarEditarComentario(${response.comentario.id})" class="text-xs text-green-500 hover:text-green-400">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="eliminarComentario(${response.comentario.id})" class="text-xs text-red-500 hover:text-red-400">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm mt-1 comentario-contenido">${response.comentario.contenido}</p>
                            <form class="editar-comentario-form hidden mt-2">
                                <div class="flex space-x-2">
                                    <input type="text" 
                                        class="flex-1 bg-gray-600 text-white rounded-lg border border-gray-500 px-3 py-1 text-sm"
                                        value="${response.comentario.contenido}">
                                    <button type="submit" onclick="editarComentario(event, ${response.comentario.id})" 
                                            class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 text-sm">
                                        Guardar
                                    </button>
                                    <button type="button" onclick="cancelarEditarComentario(${response.comentario.id})"
                                            class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                            <div class="mt-2 flex items-center">
                                <button class="comentario-like-button flex items-center space-x-2" 
                                        data-comentario-id="${response.comentario.id}"
                                        data-liked="false">
                                    <i class="text-red-500 far fa-heart"></i>
                                    <span class="text-xs comentario-like-count">0</span>
                                </button>
                            </div>
                        </div>
                    `;

                        const comentariosDiv = publicacionContainer.find('.comentarios-lista');
                        comentariosDiv.prepend(nuevoComentario);

                        // Mostrar la sección de comentarios si está oculta
                        const comentariosContainer = publicacionContainer.find(
                            `#comentarios-${publicacionId}`);
                        if (comentariosContainer.hasClass('hidden')) {
                            comentariosContainer.removeClass('hidden');
                        }

                        // Limpiar el input
                        input.val('');

                        // Actualizar el contador de comentarios
                        actualizarContadorComentarios(publicacionId, 1);
                    }
                });
            });
        });

        function actualizarContadorComentarios(publicacionId, incremento) {
            const countElement = $(`.toggle-comments[data-publicacion-id="${publicacionId}"] span`);
            const currentCount = parseInt(countElement.text()) || 0;
            countElement.text(Math.max(0, currentCount + incremento));
        }

        function mostrarEditarComentario(comentarioId) {
            const comentarioDiv = document.getElementById(`comentario-${comentarioId}`);
            const form = comentarioDiv.querySelector('.editar-comentario-form');
            const contenido = comentarioDiv.querySelector('.comentario-contenido');

            form.classList.remove('hidden');
            contenido.classList.add('hidden');
        }

        function cancelarEditarComentario(comentarioId) {
            const comentarioDiv = document.getElementById(`comentario-${comentarioId}`);
            const form = comentarioDiv.querySelector('.editar-comentario-form');
            const contenido = comentarioDiv.querySelector('.comentario-contenido');

            form.classList.add('hidden');
            contenido.classList.remove('hidden');
        }

        function editarComentario(event, comentarioId) {
            event.preventDefault();
            const comentarioDiv = document.getElementById(`comentario-${comentarioId}`);
            const form = comentarioDiv.querySelector('.editar-comentario-form');
            const input = form.querySelector('input');
            const contenido = comentarioDiv.querySelector('.comentario-contenido');

            $.ajax({
                url: `/comentario/${comentarioId}/editar`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    contenido: input.value
                },
                success: function(response) {
                    contenido.textContent = response.contenido;
                    form.classList.add('hidden');
                    contenido.classList.remove('hidden');
                },
                error: function(xhr, status, error) {
                    alert('Error al editar el comentario. Por favor, inténtalo de nuevo.');
                }
            });
        }

        function eliminarComentario(comentarioId) {
            if (!confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
                return;
            }

            const comentarioDiv = document.getElementById(`comentario-${comentarioId}`);
            const publicacionId = $(comentarioDiv).data('publicacion-id');

            $.ajax({
                url: `/comentario/${comentarioId}/eliminar`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    // Eliminar el comentario con una animación
                    $(comentarioDiv).fadeOut(300, function() {
                        $(this).remove();

                        // Actualizar el contador de comentarios
                        const contadorElement = $(
                            `.toggle-comments[data-publicacion-id="${publicacionId}"] span`);
                        const contadorActual = parseInt(contadorElement.text()) || 0;
                        contadorElement.text(Math.max(0, contadorActual - 1));

                        // Si no quedan comentarios, mostrar mensaje y/o ocultar sección
                        const comentariosLista = $(`#comentarios-${publicacionId} .comentarios-lista`);
                        if (comentariosLista.children().length === 0) {
                            comentariosLista.html(
                                '<div class="text-center text-gray-400">No hay comentarios aún</div>'
                            );
                            // Opcional: ocultar la sección completa
                            $(`#comentarios-${publicacionId}`).addClass('hidden');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    alert('Error al eliminar el comentario. Por favor, inténtalo de nuevo.');
                }
            });
        }

        // Función para actualizar todas las fechas
        function actualizarFechas() {
            $('.fecha-publicacion').each(function() {
                var timestamp = $(this).data('timestamp');
                $(this).text(moment(timestamp).fromNow());
            });
        }

        // Actualizar fechas cuando carga la página
        $(document).ready(function() {
            actualizarFechas();

            // Actualizar fechas cada minuto
            setInterval(actualizarFechas, 60000);
        });

        // Variables globales para el visor de imágenes
        const imageViewer = {
            currentImages: [],
            currentIndex: 0
        };

        // Clase para manejar la gestión de imágenes
        class ImageManager {
            constructor() {
                this.imageInput = document.getElementById('imageInput');
                this.previewContainer = document.getElementById('imagePreview');
                this.contentTextarea = document.getElementById('contenidoPublicacion');
                this.viewer = document.getElementById('imageViewer');

                this.initializeEventListeners();
            }

            initializeEventListeners() {
                // Event listeners para subida y pegado de imágenes
                this.imageInput.addEventListener('change', (e) => this.handleNewFiles(e.target.files));
                this.contentTextarea.addEventListener('paste', (e) => this.handlePastedImages(e));

                // Event listeners para el visor de imágenes
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') this.closeViewer();
                    if (e.key === 'ArrowLeft') this.navigateImage('prev');
                    if (e.key === 'ArrowRight') this.navigateImage('next');
                });
            }

            handleNewFiles(files) {
                const newFiles = [...files].filter(file => file.type.startsWith('image/'));
                if (newFiles.length) {
                    this.addFilesToInput(newFiles);
                }
            }

            handlePastedImages(e) {
                const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                const newFiles = [];

                for (let item of items) {
                    if (item.type.indexOf('image') !== -1) {
                        newFiles.push(item.getAsFile());
                    }
                }

                if (newFiles.length) {
                    this.addFilesToInput(newFiles);
                }
            }

            addFilesToInput(newFiles) {
                const dt = new DataTransfer();

                // Mantener archivos existentes
                if (this.imageInput.files) {
                    [...this.imageInput.files].forEach(file => dt.items.add(file));
                }

                // Agregar nuevos archivos
                newFiles.forEach(file => dt.items.add(file));

                this.imageInput.files = dt.files;
                this.updatePreview();
            }

            updatePreview() {
                this.previewContainer.innerHTML = '';

                [...this.imageInput.files].forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                        <button type="button" 
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                                data-index="${index}">
                            ×
                        </button>
                    `;

                        // Event listener para el botón de eliminar
                        const deleteButton = div.querySelector('button');
                        deleteButton.addEventListener('click', () => this.removeImage(index));

                        this.previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }

            removeImage(index) {
                const dt = new DataTransfer();

                [...this.imageInput.files].forEach((file, i) => {
                    if (i !== index) dt.items.add(file);
                });

                this.imageInput.files = dt.files;
                this.updatePreview();
            }

            // Métodos del visor de imágenes
            openViewer(images, index) {
                imageViewer.currentImages = images;
                imageViewer.currentIndex = index;
                this.updateViewerImage();
                this.viewer.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            closeViewer() {
                this.viewer.classList.add('hidden');
                document.body.style.overflow = '';
            }

            updateViewerImage() {
                const viewerImg = document.getElementById('viewerImage');
                viewerImg.src = `{{ asset('storage') }}/${imageViewer.currentImages[imageViewer.currentIndex]}`;
            }

            navigateImage(direction) {
                const length = imageViewer.currentImages.length;
                if (direction === 'prev') {
                    imageViewer.currentIndex = (imageViewer.currentIndex - 1 + length) % length;
                } else {
                    imageViewer.currentIndex = (imageViewer.currentIndex + 1) % length;
                }
                this.updateViewerImage();
            }
        }

        // Inicializar el gestor de imágenes cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            window.imageManager = new ImageManager();
        });

        // Exponer funciones necesarias para el HTML
        window.openImageViewer = (images, index) => window.imageManager.openViewer(images, index);
        window.closeImageViewer = () => window.imageManager.closeViewer();
        window.prevImage = () => window.imageManager.navigateImage('prev');
        window.nextImage = () => window.imageManager.navigateImage('next');
    </script>

@endsection
