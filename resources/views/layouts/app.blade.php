<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FUNDACOVI') }}</title>

    <!-- Manifest PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FBBF24">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F3EAC0] text-gray-900">
    
    <!-- Barra de Navegación (Con Alpine para móvil) -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-[#F3EAC0] border-b border-yellow-600 shadow-sm relative z-40">
        <div class="px-6 py-4 flex justify-between items-center">
            
            <!-- Izquierda: Logo y Enlaces Desktop -->
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="font-bold text-2xl text-gray-800 flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Apicultura" class="h-10 w-10 rounded-lg shadow-sm object-cover">
                    Apicultura
                </a>

                <!-- Menú Desktop -->
                <div class="hidden md:flex gap-2 font-semibold text-gray-700">
                    <a href="{{ route('dashboard') }}" class="hover:text-yellow-700 px-3 py-1 rounded-md">Inicio</a>
                    @if (auth()->user()->role->nombre_rol === 'Administrador')
                        <a href="{{ route('admin.usuarios') }}" class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Usuarios</a>
                        <a href="{{ route('admin.apiarios') }}" class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Apiarios</a>
                        <a href="{{ route('admin.colmenas') }}" class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Colmenas</a>
                    @endif
                    <a href="#" class="hover:text-yellow-700 px-3 py-1 rounded-md">Productos</a>
                </div>
            </div>

            <!-- Derecha: Perfil (Desktop) y Botón Hamburguesa (Móvil) -->
            <div class="flex items-center gap-4">
                <div class="hidden md:block">
                    <livewire:layout.navigation />
                </div>
                
                <!-- Botón Hamburguesa (Móvil) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-800 hover:text-yellow-700 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none;"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil Desplegable -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-[#E9EFA7] border-t border-yellow-500 absolute w-full shadow-lg">
            <div class="flex flex-col px-4 py-4 space-y-2 font-bold text-gray-800">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-yellow-300">Inicio</a>
                @if (auth()->user()->role->nombre_rol === 'Administrador')
                    <a href="{{ route('admin.usuarios') }}" class="block px-3 py-2 rounded-md bg-yellow-400">Usuarios</a>
                    <a href="{{ route('admin.apiarios') }}" class="block px-3 py-2 rounded-md bg-yellow-400">Apiarios</a>
                    <a href="{{ route('admin.colmenas') }}" class="block px-3 py-2 rounded-md bg-yellow-400">Colmenas</a>
                @endif
                <a href="#" class="block px-3 py-2 rounded-md hover:bg-yellow-300">Productos</a>
                <hr class="border-yellow-500 my-2">
                <!-- Para móvil, mostramos el componente de navegación que ya maneja perfil/logout -->
                <div class="px-3">
                    <livewire:layout.navigation />
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <main class="py-8 px-4 md:px-6 max-w-7xl mx-auto">
        {{ $slot }}
    </main>

    <x-network-status /> 
    <x-toast />
</body>
</html>
