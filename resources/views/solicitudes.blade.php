@extends('base')

@section('title', 'Solicitudes de Amistad')

@section('content')
<div class="bg-gray-900 rounded-xl shadow-2xl p-8 text-white">
    <h1 class="text-3xl font-extrabold text-center mb-8 tracking-wide">Solicitudes de Amistad</h1>

    <div class="space-y-6">
        @forelse($solicitudes as $solicitud)
            <div class="flex items-center justify-between p-4 bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center space-x-6">
                    <!-- Foto de perfil -->
                    <img src="{{ asset('storage/fotos_perfil/' . $solicitud->foto_perfil) }}" alt="Foto de {{ $solicitud->nombres }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-600 shadow-sm hover:shadow-md transition duration-200">

                    <!-- Nombre y correo -->
                    <div>
                        <a href="{{ route('ver.perfil', $solicitud->usuario_id) }}" class="text-lg font-semibold text-blue-400 hover:underline">
                            {{ $solicitud->nombres }}
                        </a>
                        <p class="text-sm text-gray-400">{{ $solicitud->correo }}</p>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <!-- Botón Aceptar -->
                    <form action="{{ route('aceptar.solicitud') }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <input type="hidden" name="amigo_id" value="{{ $solicitud->usuario_id }}">
                        <button type="submit" class="px-5 py-2 border-2 border-green-500 text-green-500 rounded-full hover:bg-green-500 hover:text-white focus:outline-none transition-all duration-300 transform hover:scale-105">
                            Aceptar
                        </button>
                    </form>

                    <!-- Botón Rechazar -->
                    <form action="{{ route('rechazar.solicitud') }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <input type="hidden" name="amigo_id" value="{{ $solicitud->usuario_id }}">
                        <button type="submit" class="px-5 py-2 border-2 border-red-500 text-red-500 rounded-full hover:bg-red-500 hover:text-white focus:outline-none transition-all duration-300 transform hover:scale-105">
                            Rechazar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-400 italic">No tienes solicitudes de amistad pendientes.</p>
        @endforelse
    </div>
</div>
@endsection
