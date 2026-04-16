<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FUNDACOVI') }}</title>

    <!-- Manifest PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#FBBF24">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F3EAC0] text-gray-900">

    <!-- Barra de Navegación -->
    <nav class="bg-[#F3EAC0] border-b border-yellow-600 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-6">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="font-bold text-2xl text-gray-800 flex items-center gap-3">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Apicultura"
                    class="h-10 w-10 rounded-lg shadow-sm object-cover">
                Apicultura
            </a>

            <div class="hidden md:flex gap-4 font-semibold text-gray-700">
                <a href="{{ route('dashboard') }}" class="hover:text-yellow-700 px-3 py-1 rounded-md">Inicio</a>
                @if (auth()->user()->role->nombre_rol === 'Administrador')
                    <a href="{{ route('admin.usuarios') }}"
                        class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Usuarios</a>
                @endif
                <a href="#" class="hover:text-yellow-700 px-3 py-1 rounded-md">Productos</a>

            </div>
        </div>

        <!-- Menú Usuario -->
        <div class="flex items-center gap-4">
            <livewire:layout.navigation />
        </div>
    </nav>

    <!-- Contenido -->
    <main class="py-8 px-6 max-w-7xl mx-auto">
        {{ $slot }}
    </main>

    <!-- Monitor Offline (Alpine.js) -->
    <div x-data="{ online: navigator.onLine }" @online.window="online = true" @offline.window="online = false"
        class="fixed bottom-4 right-4 z-50">
        <template x-if="!online">
            <div
                class="bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 font-bold animate-pulse">
                ⚠️ MODO OFFLINE: Datos se guardarán localmente
            </div>
        </template>
    </div>
</body>

</html>
