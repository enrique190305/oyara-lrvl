<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Q'hubo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Preload fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts y Estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        },
                        dark: {
                            800: '#1f2937',
                            900: '#111827',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.js"></script>
</head>
<body class="h-full bg-dark-900 text-gray-100 font-sans antialiased">
    <!-- Navbar -->
    <nav class="bg-dark-800 border-b border-gray-700 relative z-20" x-data="{ open: false }"> <!-- Agrega relative y z-20 -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo y Navegación Principal -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <span class="text-2xl font-bold bg-gradient-to-r from-primary-500 to-primary-600 text-transparent bg-clip-text">Q'hubo</span>
                    </a>
                </div>

                <!-- Búsqueda y Menú -->
                <div class="flex items-center gap-4">
                    <!-- Búsqueda -->
                    <div class="relative" x-data="{ results: [], searchQuery: '' }">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                @input.debounce.300ms="
                                    fetch(`/buscar?busqueda=${encodeURIComponent(searchQuery)}`)
                                        .then(res => res.json())
                                        .then(data => results = data)
                                "
                                x-model="searchQuery"
                                class="w-80 pl-10 pr-4 py-2 bg-dark-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                placeholder="Buscar usuarios...">
                        </div>
                        
                        <!-- Resultados de búsqueda -->
                        <div class="absolute z-50 w-full mt-2 bg-dark-800 rounded-lg shadow-xl border border-gray-700" 
                            x-show="results.length > 0" 
                            x-cloak
                            @click.away="results = []">
                            <template x-for="user in results" :key="user.id">
                                <a :href="'/perfil/' + user.id"
                                    class="flex items-center gap-3 p-3 hover:bg-dark-900 transition-colors">
                                    <img :src="'/storage/fotos_perfil/' + user.foto_perfil" 
                                        :alt="user.nombres"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-gray-700">
                                    <div class="flex flex-col">
                                        <span class="font-medium" x-text="user.nombres"></span>
                                        <span class="text-sm text-gray-400" x-text="user.correo"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Enlaces de Navegación -->
                    <nav class="flex items-center gap-6">
                        <a href="{{ route('ver.solicitudes') }}" 
                            class="relative group flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-user-plus"></i>
                            <span>Solicitudes</span>
                            @if($pendingRequests > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                    {{ $pendingRequests }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('ver.amigos') }}" 
                            class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-users"></i>
                            <span>Amigos</span>
                        </a>
                        <a href="{{ route('home') }}" 
                            class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-home"></i>
                            <span>Inicio</span>
                        </a>
                    </nav>

                    <!-- Menú de Usuario -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                            class="flex items-center gap-2 p-2 rounded-lg hover:bg-dark-900 transition-colors">
                            <span class="font-medium text-primary-500">{{ session('user_name') }}</span>
                            <i class="fas fa-chevron-down text-sm transition-transform"
                                :class="{ 'transform rotate-180': open }"></i>
                        </button>
                        
                        <div x-show="open" 
                            x-cloak
                            @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-dark-800 rounded-lg shadow-xl border border-gray-700 overflow-hidden">
                            <form method="GET" action="{{ route('ver.perfil', ['id' => session('user_id')]) }}">
                                @csrf
                                <button type="submit" 
                                    class="w-full px-4 py-3 text-left text-sm hover:bg-dark-900 transition-colors">
                                    <i class="fas fa-user mr-2"></i>
                                    Mi perfil
                                </button>
                            </form>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                    class="w-full px-4 py-3 text-left text-sm text-red-500 hover:bg-red-500 hover:text-white transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if($errors->any())
            <div id="success" class="bg-gray-500/10 border border-gray-500/20 rounded-lg p-4 mb-6">
                <div class="flex gap-3">
                    <i class="fas fa-exclamation-circle text-gray-500 mt-1"></i>
                    <div class="text-gray-200">
                        {!! implode('<br>', $errors->all()) !!}
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Visor de Imágenes -->
    <div id="imageViewer" class="fixed inset-0 bg-black/95 hidden z-50">
        <button class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-white hover:text-gray-300 transition-colors" onclick="closeImageViewer()">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <button class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center text-white hover:text-gray-300 transition-colors" onclick="prevImage()">
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>
        <button class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center text-white hover:text-gray-300 transition-colors" onclick="nextImage()">
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>
        <div class="flex items-center justify-center h-full">
            <img id="viewerImage" src="" alt="Imagen ampliada" class="max-h-[90vh] max-w-[90vw] object-contain">
        </div>
    </div>

    <!-- Chat Flotante -->
    <div x-data="{ 
        open: false,
        amigos: [],
        totalNoLeidos: 0,
        loadAmigos() {
            fetch('{{ route('amigos.mensajes') }}')
                .then(res => res.json())
                .then(data => {
                    this.amigos = data;
                    this.actualizarTotalNoLeidos();
                })
        },
        actualizarTotalNoLeidos() {
            fetch('{{ route('mensajes.no.leidos') }}')
                .then(res => res.json())
                .then(data => this.totalNoLeidos = data.total)
        },
        iniciarActualizacionAutomatica() {
            setInterval(() => {
                if (!this.open) {
                    this.actualizarTotalNoLeidos();
                }
            }, 10000);
        }
    }" 
    @click.away="open = false"
    x-init="actualizarTotalNoLeidos(); iniciarActualizacionAutomatica()"
    class="fixed bottom-6 right-6">
        
        <!-- Botón del Chat -->
        <button @click="open = !open; if(open) loadAmigos()"
            class="bg-primary-600 hover:bg-primary-700 w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-colors relative">
            <i class="fas fa-comments text-xl"></i>
            <template x-if="totalNoLeidos > 0">
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full min-w-[22px] h-6 flex items-center justify-center px-2"
                    x-text="totalNoLeidos"></span>
            </template>
        </button>

        <!-- Menú del Chat -->
        <div x-show="open"
            x-cloak
            class="absolute bottom-20 right-0 w-72 bg-dark-800 rounded-lg shadow-xl border border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-comments text-primary-500"></i>
                    Chats
                </h3>
            </div>
            <div class="max-h-96 overflow-y-auto">
                <template x-for="amigo in amigos" :key="amigo.id">
                    <a :href="'/chat/' + amigo.id"
                        class="flex items-center justify-between p-4 hover:bg-dark-900 transition-colors border-b border-gray-700/50">
                        <div class="flex items-center gap-3">
                            <!-- Foto de perfil -->
                            <div class="w-10 h-10 rounded-full overflow-hidden">
                                <template x-if="amigo.foto_perfil">
                                    <img :src="amigo.foto_perfil" 
                                        :alt="'Foto de ' + amigo.nombres"
                                        class="w-full h-full object-cover">
                                </template>
                                <template x-if="!amigo.foto_perfil">
                                    <img src="/img/default-avatar.png" 
                                        alt="Foto de perfil por defecto"
                                        class="w-full h-full object-cover">
                                </template>
                            </div>
                            <span x-text="amigo.nombres" class="font-medium"></span>
                        </div>
                        <template x-if="amigo.mensajes_nuevos > 0">
                            <span class="bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1 min-w-[20px] text-center"
                                x-text="amigo.mensajes_nuevos"></span>
                        </template>
                    </a>
                </template>
            </div>
        </div>
    </div>
    <script>
        // JavaScript para ocultar el mensaje de éxito después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success');
            
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 4000);
            }
        });

        $(document).ready(function () {
            $('.like-button').click(function (e) {
                e.preventDefault();
                var button = $(this);
                var publicacionId = button.data('publicacion-id');
                var liked = button.data('liked');

                var url = liked ? `/quitar-like/${publicacionId}` : `/like-publicacion/${publicacionId}`;
                var method = liked ? 'DELETE' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        button.data('liked', !liked);
                        button.find('i').toggleClass('fas far');
                    },
                    error: function (xhr) {
                        console.error('Error en la solicitud:', xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>