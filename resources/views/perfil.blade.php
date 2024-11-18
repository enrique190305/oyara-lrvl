@extends('base')

@section('title', $usuario->nombres)

@section('content')
    <div class="space-y-6">
        <!-- Header Card -->
        <div class="bg-dark-800 rounded-xl shadow-xl overflow-hidden relative">
            <!-- Banner -->
            <div class="relative h-48">
                @php
                    $bannerPath = $usuario->banner_imagen
                        ? '/storage/banners/' . $usuario->banner_imagen
                        : '/storage/banners/default_banner.jpg';
                @endphp
                
                <div class="absolute inset-0 bg-gradient-to-r from-primary-600 to-primary-700">
                    <img src="{{ $bannerPath }}" 
                        alt="Banner de perfil" 
                        class="w-full h-full object-cover"
                        onerror="this.style.display='none'">
                </div>

                @if ($usuario->id === session('user_id'))
                    <form action="{{ route('actualizar.banner') }}" method="POST" enctype="multipart/form-data"
                        class="absolute top-4 right-4">
                        @csrf
                        <label class="cursor-pointer bg-dark-800/50 hover:bg-dark-800/70 text-white px-3 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2 text-sm">
                            <i class="fas fa-image text-sm"></i>
                            <span>Cambiar banner</span>
                            <input type="file" name="banner" class="hidden" onchange="this.form.submit()" accept="image/*">
                        </label>
                    </form>
                @endif
            </div>

            <!-- Profile Info -->
            <div class="relative px-6 pb-6">
                <!-- Profile Picture -->
                <div class="relative -mt-16 mb-4">
                    <div class="relative inline-block group">
                        @php
                            $fotoPath = $usuario->foto_perfil
                                ? '/storage/fotos_perfil/' . $usuario->foto_perfil
                                : '/storage/fotos_perfil/default.jpg';
                        @endphp

                        <img src="{{ $fotoPath }}" alt="Foto de perfil de {{ $usuario->nombres }}"
                            class="w-32 h-32 rounded-xl object-cover border-4 border-dark-800 shadow-lg"
                            onerror="this.src='/storage/fotos_perfil/default.jpg'">

                        @if ($usuario->id === session('user_id'))
                            <form action="{{ route('actualizar.foto') }}" method="POST" enctype="multipart/form-data"
                                class="opacity-0 group-hover:opacity-100 transition-opacity absolute inset-0 flex items-center justify-center bg-black/50 rounded-xl">
                                @csrf
                                <label class="cursor-pointer text-white text-sm font-medium">
                                    <i class="fas fa-camera mr-2"></i>
                                    Cambiar
                                    <input type="file" name="foto" class="hidden" onchange="this.form.submit()" accept="image/*">
                                </label>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold">{{ $usuario->nombres }}</h1>
                        <p class="text-gray-400 flex items-center gap-2 mt-1">
                            <i class="fas fa-fingerprint text-sm"></i>
                            <span>{{ $usuario->id }}</span>
                            <span class="text-gray-600">•</span>
                            <i class="fas fa-envelope text-sm"></i>
                            <span>{{ $usuario->correo }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Friendship Actions -->
            <div class="absolute bottom-4 right-4 flex flex-col space-y-2">
                @if($usuario->id !== session('user_id'))
                    @if(!$estadoAmistad)
                        <form action="{{ route('enviar.solicitud') }}" method="POST">
                            @csrf
                            <input type="hidden" name="amigo_id" value="{{ $usuario->id }}">
                            <button type="submit" class="bg-transparent text-blue-600 border-2 border-blue-600 px-6 py-3 rounded-full font-semibold hover:bg-blue-600 hover:text-white hover:border-transparent transition-all duration-300 ease-in-out">
                                Enviar Solicitud
                            </button>
                        </form>
                    @elseif($estadoAmistad->estado === 'pendiente')
                        <span class="text-yellow-400">Solicitud Pendiente</span>
                    @elseif($estadoAmistad->estado === 'aceptado')
                        <a href="{{ route('ver.chat', $usuario->id) }}" 
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center">
                            <i class="fas fa-comment-alt"></i>
                        </a>
                    @endif
                @endif
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-1 space-y-6">
                <!-- About Section -->
                <div class="bg-dark-800 rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold flex items-center gap-2">
                            <i class="fas fa-user text-primary-500"></i>
                            Sobre mí
                        </h2>
                        @if ($usuario->id === session('user_id'))
                            <button onclick="toggleEdit('descripcion')"
                                class="text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif
                    </div>

                    <div id="descripcion-view" class="text-gray-300">
                        {{ $usuario->descripcion ?? 'Sin descripción' }}
                    </div>


                    @if ($usuario->id === session('user_id'))
                        <div id="descripcion-edit" class="hidden">
                            <form action="{{ route('actualizar.perfil') }}" method="POST" class="space-y-4">
                                @csrf
                                <textarea name="descripcion"
                                    class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                    rows="4" placeholder="Escribe algo sobre ti...">{{ $usuario->descripcion }}</textarea>
                                <button type="submit"
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                                    Guardar cambios
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Quick Info -->
                <div class="bg-dark-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold flex items-center gap-2 mb-4">
                        <i class="fas fa-info-circle text-primary-500"></i>
                        Información rápida
                    </h2>
                    <div class="space-y-4">
                        @if ($usuario->fecha_nacimiento)
                            <div class="flex items-center gap-3">
                                <i class="fas fa-birthday-cake text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-400">Edad / Cumpleaños</p>
                                    <p class="text-white">
                                        {{ \Carbon\Carbon::parse($usuario->fecha_nacimiento)->age }} / 
                                        {{ \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d-m-Y') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($usuario->ubicacion)
                            <div class="flex items-center gap-3">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-400">Dirección</p>
                                    <p class="text-white">{{ $usuario->ubicacion }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($usuario->ocupacion)
                            <div class="flex items-center gap-3">
                                <i class="fas fa-briefcase text-gray-400"></i>
                                <div>
                                    <p class="text-sm text-gray-400">Ocupación</p>
                                    <p class="text-white">{{ $usuario->ocupacion }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-2">
                <!-- Detailed Info Card -->
                <div class="bg-dark-800 rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold flex items-center gap-2">
                            <i class="fas fa-address-card text-primary-500"></i>
                            Información detallada
                        </h2>
                        @if ($usuario->id === session('user_id'))
                            <button onclick="toggleEdit('info')" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif
                    </div>

                    <div id="info-view" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Centro de Estudios -->
                        <div class="space-y-2">
                            <p class="text-sm text-gray-400">Centro de Estudios</p>
                            <p class="text-white flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-gray-400"></i>
                                {{ $usuario->centro_estudios ?? 'No especificado' }}
                            </p>
                        </div>

                        <!-- Género -->
                        <div class="space-y-2">
                            <p class="text-sm text-gray-400">Género</p>
                            <p class="text-white flex items-center gap-2">
                                <i class="fas fa-venus-mars text-gray-400"></i>
                                {{ $usuario->genero ?? 'No especificado' }}
                            </p>
                        </div>

                        <!-- Centro de Trabajo -->
                        <div class="space-y-2">
                            <p class="text-sm text-gray-400">Centro de Trabajo</p>
                            <p class="text-white flex items-center gap-2">
                                <i class="fas fa-briefcase text-gray-400"></i>
                                {{ $usuario->centro_trabajo ?? 'No especificado' }}
                            </p>
                        </div>

                        <!-- Intereses -->
                        <div class="space-y-2">
                            <p class="text-sm text-gray-400">Intereses</p>
                            <p class="text-white flex items-center gap-2">
                                <i class="fas fa-heart text-gray-400"></i>
                                {{ $usuario->intereses ?? 'No especificados' }}
                            </p>
                        </div>
                    </div>

                    @if ($usuario->id === session('user_id'))
                        <div id="info-edit" class="hidden">
                            <form action="{{ route('actualizar.perfil') }}" method="POST" class="space-y-6">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Fecha de Nacimiento -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">
                                            Fecha de Nacimiento
                                        </label>
                                        <input type="date" name="fecha_nacimiento"
                                            value="{{ $usuario->fecha_nacimiento }}"
                                            class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                    </div>

                                    <!-- Centro de Estudios -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">
                                            Centro de Estudios
                                        </label>
                                        <input type="text" name="centro_estudios"
                                            value="{{ $usuario->centro_estudios }}"
                                            class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                    </div>

                                    <!-- Centro de Trabajo -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">
                                            Centro de Trabajo
                                        </label>
                                        <input type="text" name="centro_trabajo"
                                            value="{{ $usuario->centro_trabajo }}"
                                            class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                    </div>

                                    <!-- Género -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">
                                            Género
                                        </label>
                                        <select name="genero"
                                            class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                            <option value="">Seleccionar...</option>
                                            <option value="Masculino"
                                                {{ $usuario->genero === 'Masculino' ? 'selected' : '' }}>
                                                Masculino
                                            </option>
                                            <option value="Femenino"
                                                {{ $usuario->genero === 'Femenino' ? 'selected' : '' }}>
                                                Femenino
                                            </option>
                                            <option value="Inventado :v"
                                                {{ $usuario->genero === 'Inventado :v' ? 'selected' : '' }}>
                                                Inventado :v
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Ubicación -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">
                                            Dirección
                                        </label>
                                        <input type="text" name="ubicacion" value="{{ $usuario->ubicacion }}"
                                            class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                    </div>

                                    <!-- Ocupación -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">
                                            Ocupación
                                        </label>
                                        <input type="text" name="ocupacion" value="{{ $usuario->ocupacion }}"
                                            class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                    </div>

                                    <!-- Intereses -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-400 mb-2">
                                            Intereses
                                        </label>
                                        <textarea name="intereses" rows="3"
                                            class="w-full bg-dark-900 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">{{ $usuario->intereses }}</textarea>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-2 rounded-lg transition-colors">
                                        Guardar cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if (session('success'))
        <div id="success-message"
            class="fixed bottom-4 right-4 bg-green-500/10 border border-green-500/20 text-green-200 px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div id="error-message"
            class="fixed bottom-4 right-4 bg-red-500/10 border border-red-500/20 text-red-200 px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-400"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <script>
        function toggleEdit(section) {
            const viewElement = document.getElementById(`${section}-view`);
            const editElement = document.getElementById(`${section}-edit`);

            if (viewElement.classList.contains('hidden')) {
                viewElement.classList.remove('hidden');
                editElement.classList.add('hidden');
            } else {
                viewElement.classList.add('hidden');
                editElement.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');

            [successMessage, errorMessage].forEach(element => {
                if (element) {
                    setTimeout(() => element.style.display = 'none', 4000);
                }
            });
        });
    </script>
@endsection
