<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - {{ config('app.name', 'FUNDACOVI') }}</title>

    <!-- Manifest PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FBBF24">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Prevenimos scroll en el body para desktop -->

<body class="font-sans antialiased bg-[#F3EAC0] text-gray-900 overflow-x-hidden h-screen flex flex-col">

    <!-- Barra de Navegación (Igual que app.blade.php) -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-[#F3EAC0] border-b border-yellow-600 shadow-sm relative z-40 shrink-0">
        <div class="px-6 py-3 flex justify-between items-center">

            <!-- Izquierda: Logo y Enlaces Desktop -->
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="font-bold text-2xl text-gray-800 flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" onerror="this.style.display='none'" alt="Logo"
                        class="h-10 w-10 rounded-lg shadow-sm object-cover">
                    Apicultura
                </a>

                <!-- Menú Desktop Separado por Roles -->
                <div class="hidden md:flex gap-2 font-semibold text-gray-700 items-center">
                    <a href="{{ route('dashboard') }}"
                        class="hover:text-yellow-700 px-3 py-1 rounded-md transition">Inicio</a>

                    <!-- MENÚ ADMINISTRADOR -->
                    @if (auth()->user()->role->nombre_rol === 'Administrador')
                        <div class="h-5 w-px bg-yellow-400 mx-1"></div> <!-- Separador visual -->
                        <a href="{{ route('admin.usuarios') }}"
                            class="hover:text-yellow-800 hover:bg-yellow-300 bg-yellow-400 px-3 py-1 rounded-md transition shadow-sm">Usuarios</a>
                        <a href="{{ route('admin.estandares') }}"
                            class="hover:text-yellow-800 hover:bg-yellow-300 bg-yellow-400 px-3 py-1 rounded-md transition shadow-sm">Estándares</a>
                        <a href="{{ route('admin.reportes') }}"
                            class="hover:text-yellow-800 hover:bg-yellow-300 bg-yellow-400 px-3 py-1 rounded-md transition shadow-sm">Reportes</a>

                        <div class="h-5 w-px bg-yellow-400 mx-1"></div>
                        <!-- Vista Global Administrativa -->
                        <a href="{{ route('admin.apiarios') }}"
                            class="hover:text-yellow-700 px-3 py-1 rounded-md transition">Apiarios</a>
                        <a href="{{ route('admin.colmenas') }}"
                            class="hover:text-yellow-700 px-3 py-1 rounded-md transition">Colmenas</a>

                        <!-- MENÚ APICULTOR -->
                    @elseif (auth()->user()->role->nombre_rol === 'Apicultor')
                        <div class="h-5 w-px bg-yellow-400 mx-1"></div>
                        <a href="{{ route('admin.apiarios') }}"
                            class="hover:bg-yellow-200 px-3 py-1 rounded-md transition">Apiarios</a>
                        <a href="{{ route('admin.colmenas') }}"
                            class="hover:bg-yellow-200 px-3 py-1 rounded-md transition">Colmenas</a>
                        <a href="{{ route('apicultor.cosechas') }}"
                            class="hover:text-yellow-800 hover:bg-yellow-300 bg-yellow-400 px-3 py-1 rounded-md transition shadow-sm">Cosechas</a>
                        <a href="{{ route('apicultor.calidad') }}"
                            class="hover:text-yellow-800 hover:bg-yellow-300 bg-yellow-400 px-3 py-1 rounded-md transition shadow-sm">Calidad</a>
                        <a href="{{ route('apicultor.productos') }}"
                            class="hover:text-yellow-800 hover:bg-yellow-300 bg-yellow-400 px-3 py-1 rounded-md transition shadow-sm">Productos</a>
                    @endif
                </div>
            </div>

            <!-- Derecha: Ayuda, Perfil y Botón Móvil -->
            <div class="flex items-center gap-2 md:gap-4">

                <!-- BOTÓN DE AYUDA -->
                <button @click="$dispatch('open-help-modal')"
                    class="text-yellow-600 hover:text-yellow-800 bg-yellow-200 hover:bg-yellow-300 p-2 rounded-full transition shadow-sm"
                    title="Centro de Ayuda">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </button>

                <div class="hidden md:block">
                    <livewire:layout.navigation />
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden text-gray-800 hover:text-yellow-700 focus:outline-none p-1">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" style="display:none;"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil Desplegable -->
        <div x-show="mobileMenuOpen" x-transition style="display:none;"
            class="md:hidden bg-[#E9EFA7] border-t border-yellow-500 absolute w-full shadow-2xl z-50">
            <div class="flex flex-col px-4 py-4 space-y-1 font-bold text-gray-800">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-yellow-300">Inicio</a>

                @if (auth()->user()->role->nombre_rol === 'Administrador')
                    <div class="text-xs text-yellow-700 font-black uppercase tracking-widest mt-2 pl-3">Gestión General
                    </div>
                    <a href="{{ route('admin.usuarios') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Usuarios</a>
                    <a href="{{ route('admin.estandares') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Estándares</a>
                    <a href="{{ route('admin.reportes') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Reportes</a>
                    <div class="text-xs text-yellow-700 font-black uppercase tracking-widest mt-2 pl-3">Módulos
                        Productivos</div>
                    <a href="{{ route('admin.apiarios') }}"
                        class="block px-3 py-2 rounded-md hover:bg-yellow-300">Apiarios</a>
                    <a href="{{ route('admin.colmenas') }}"
                        class="block px-3 py-2 rounded-md hover:bg-yellow-300">Colmenas</a>
                @elseif (auth()->user()->role->nombre_rol === 'Apicultor')
                    <div class="text-xs text-yellow-700 font-black uppercase tracking-widest mt-2 pl-3">Mis Entornos
                    </div>
                    <a href="{{ route('admin.apiarios') }}"
                        class="block px-3 py-2 rounded-md hover:bg-yellow-300">Apiarios</a>
                    <a href="{{ route('admin.colmenas') }}"
                        class="block px-3 py-2 rounded-md hover:bg-yellow-300">Colmenas</a>
                    <div class="text-xs text-yellow-700 font-black uppercase tracking-widest mt-2 pl-3">Producción</div>
                    <a href="{{ route('apicultor.cosechas') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Cosechas</a>
                    <a href="{{ route('apicultor.calidad') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Calidad</a>
                    <a href="{{ route('apicultor.productos') }}"
                        class="block px-3 py-2 rounded-md bg-yellow-400">Productos</a>
                @endif

                <hr class="border-yellow-500 my-3">
                <div class="px-2">
                    <livewire:layout.navigation />
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Libre -->
    <main class="flex-1 flex flex-col w-full h-full overflow-y-auto lg:overflow-hidden">
        {{ $slot }}
    </main>

    <x-toast />
    <x-network-status />
    <!-- Componente Global de Ayuda -->
    <livewire:ayuda-modal />
</body>

</html>
