<!-- amigos.blade.php -->
@extends('base')

@section('title', 'Mis Amigos')

@section('content')
<div class="bg-gray-800 rounded-lg shadow-lg p-6 text-white">
    <h1 class="text-2xl font-bold mb-6">Mis Amigos</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($amigos as $amigo)
            <div class="bg-gray-700 border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="p-4">
                    <div class="flex items-center space-x-4">
                        <!-- Foto de perfil -->
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

                        <!-- Información del amigo -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold truncate">
                                <a href="{{ route('ver.perfil', $amigo->id) }}" class="text-cyan-400 hover:underline">
                                    {{ $amigo->nombres }}
                                </a>
                            </h3>
                            <p class="text-gray-300 text-sm truncate">ID: {{ $amigo->id }} | {{ $amigo->correo }}</p>
                        </div>

                        <!-- Botón de chat -->
                        <a href="{{ route('ver.chat', $amigo->id) }}" 
                            class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 bg-cyan-600 text-white rounded-full hover:bg-red-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-400">Aún no tienes amigos agregados</p>
                <p class="text-sm text-gray-500 mt-2">¡Usa la barra de búsqueda para encontrar personas!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection