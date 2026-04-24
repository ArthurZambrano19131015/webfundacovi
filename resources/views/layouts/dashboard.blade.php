<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ config('app.name', 'FUNDACOVI') }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FBBF24">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<!-- Prevenimos scroll en el body para desktop -->
<body class="font-sans antialiased bg-[#F3EAC0] text-gray-900 overflow-x-hidden h-screen flex flex-col">
    
    <!-- Navbar (Igual al de app.blade.php) -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-[#F3EAC0] border-b border-yellow-600 shadow-sm relative z-40 shrink-0">
        <div class="px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-bold text-2xl text-gray-800 flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" onerror="this.style.display='none'" alt="Logo" class="h-10 w-10 rounded-lg shadow-sm object-cover">
                    Apicultura
                </a>
                <div class="hidden md:flex gap-2 font-semibold text-gray-700">
                    <a href="{{ route('dashboard') }}" class="hover:text-yellow-700 px-3 py-1 rounded-md">Inicio</a>
                    @if (auth()->user()->role->nombre_rol === 'Administrador')
                        <a href="{{ route('admin.usuarios') }}"
                            class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Usuarios</a>
                        <a href="{{ route('admin.apiarios') }}"
                            class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Apiarios</a>
                        <a href="{{ route('admin.colmenas') }}"
                            class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Colmenas</a>
                        <a href="{{ route('apicultor.cosechas') }}"
                            class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Cosechas</a>
                        <a href="{{ route('apicultor.calidad') }}"
                            class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Calidad</a>
                        <a href="{{ route('admin.estandares') }}"
                            class="hover:text-yellow-700 bg-yellow-400 px-3 py-1 rounded-md">Estandares</a>
                    @endif
                    <a href="#" class="hover:text-yellow-700 px-3 py-1 rounded-md">Productos</a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:block"><livewire:layout.navigation /></div>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-800 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none;"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-[#E9EFA7] border-t border-yellow-500 absolute w-full shadow-lg">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-yellow-300">Inicio</a>
                @if (auth()->user()->role->nombre_rol === 'Administrador')
                    <a href="{{ route('admin.usuarios') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Usuarios</a>
                    <a href="{{ route('admin.apiarios') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Apiarios</a>
                    <a href="{{ route('admin.colmenas') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Colmenas</a>
                    <a href="{{ route('apicultor.cosechas') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Cosechas</a>
                    <a href="{{ route('admin.estandares') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Estandares</a>
                    <a href="{{ route('apicultor.calidad') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Calidad</a>
                @endif
                <a href="#" class="block px-3 py-2 rounded-md hover:bg-yellow-300">Productos</a>
            <div class="px-3 py-4"><livewire:layout.navigation /></div>
        </div>
    </nav>

    <!-- Contenido Libre (Sin restricciones de padding general) -->
    <main class="flex-1 flex flex-col w-full h-full overflow-y-auto lg:overflow-hidden">
        {{ $slot }}
    </main>

    <x-network-status /> 
    <x-toast />
</body>
</html>