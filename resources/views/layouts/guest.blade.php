<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FUNDACOVI - Pro-Productos Apícolas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased h-screen flex flex-col bg-[#F3EAC0]">
    
    <nav x-data="{ mobileMenuOpen: false }" class="bg-[#E9EFA7] px-6 py-3 shadow-sm relative z-40">
        <div class="flex justify-between items-center">
            <!-- Logo local -->
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Apicultura" class="h-12 w-12 rounded-xl shadow-md object-cover">
                <span class="font-black text-xl md:text-2xl text-black tracking-wide">Apicultura</span>
            </div>

            <!-- Enlaces centrales (Desktop) -->
            <div class="hidden md:flex items-center gap-6 font-bold text-black">
                <a href="/" class="hover:text-gray-700">Inicio</a>
                <a href="#" class="border-2 border-gray-600 rounded-full px-4 py-1 hover:bg-gray-200 transition">Productos</a>
                <a href="#" class="hover:text-gray-700">Apicultores</a>
                <a href="#" class="hover:text-gray-700">Contacto</a>
            </div>

            <!-- Botón Ingresar (Desktop) -->
            <div class="hidden md:flex items-center">
                <a href="{{ route('login') }}" class="bg-[#CFCFCF] text-black font-bold px-6 py-2 rounded-full border border-gray-400 shadow-sm hover:bg-gray-300 transition">
                    Ingresar
                </a>
            </div>

            <!-- Hamburguesa (Móvil) -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-black focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none;"></path>
                </svg>
            </button>
        </div>

        <!-- Menú Móvil Desplegable -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden absolute w-full left-0 top-full bg-[#E9EFA7] border-t border-gray-300 shadow-lg">
            <div class="flex flex-col px-6 py-4 space-y-3 font-bold text-black text-center">
                <a href="/" class="block py-2 hover:bg-yellow-100 rounded">Inicio</a>
                <a href="#" class="block py-2 bg-yellow-200 rounded">Productos</a>
                <a href="#" class="block py-2 hover:bg-yellow-100 rounded">Apicultores</a>
                <a href="#" class="block py-2 hover:bg-yellow-100 rounded">Contacto</a>
                <a href="{{ route('login') }}" class="block py-3 mt-2 bg-[#CFCFCF] border border-gray-400 rounded-lg shadow-sm">Ingresar</a>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO DE LA PÁGINA  -->
    <main class="flex-1 flex flex-col w-full h-full">
        {{ $slot }}
    </main>

    <x-network-status /> 
    <x-toast />
</body>
</html>