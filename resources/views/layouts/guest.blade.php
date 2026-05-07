<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FUNDACOVI - Productos Apícolas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased h-screen flex flex-col bg-[#F3EAC0]">
    
    <!-- Navbar reducido en altura  -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-[#E9EFA7] px-6 py-2 shadow-sm relative z-50 shrink-0">
        
        <!-- Usamos Grid de 3 columnas para centrado absoluto -->
        <div class="grid grid-cols-2 md:grid-cols-3 items-center">
            
            <!-- Izquierda: Logo -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/logo.png') }}" onerror="this.style.display='none'" alt="Logo" class="h-10 w-10 rounded-lg shadow-md object-cover">
                <span class="font-black text-xl text-black tracking-wide">Apicultura</span>
            </div>

            <!-- Centro: Enlaces Anclados (Ocultos en móvil) -->
            <div class="hidden md:flex justify-center items-center gap-6 font-bold text-black text-sm">
                <a href="#inicio" class="hover:text-yellow-700 transition">Inicio</a>
                <a href="#productos" class="border-2 border-gray-600 rounded-full px-4 py-1 hover:bg-white hover:border-white transition">Productos</a>
                <a href="#apicultores" class="hover:text-yellow-700 transition">Apicultores</a>
                <a href="#contacto" class="hover:text-yellow-700 transition">Contacto</a>
            </div>

            <!-- Derecha: Botón Ingresar -->
            <div class="flex justify-end items-center">
                <a href="{{ route('login') }}" class="hidden md:inline-block bg-[#CFCFCF] text-black font-bold px-6 py-1.5 rounded-full border border-gray-400 shadow-sm hover:bg-gray-300 transition text-sm">
                    Ingresar
                </a>
                
                <!-- Hamburguesa (Móvil) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-black focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none;"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil Desplegable -->
        <div x-show="mobileMenuOpen" x-transition style="display:none;" class="md:hidden absolute w-full left-0 top-full bg-[#E9EFA7] border-t border-gray-300 shadow-lg">
            <div class="flex flex-col px-6 py-4 space-y-2 font-bold text-black text-center text-sm">
                <a href="#inicio" @click="mobileMenuOpen = false" class="block py-2 hover:bg-yellow-100 rounded">Inicio</a>
                <a href="#productos" @click="mobileMenuOpen = false" class="block py-2 bg-yellow-200 rounded">Productos</a>
                <a href="#apicultores" @click="mobileMenuOpen = false" class="block py-2 hover:bg-yellow-100 rounded">Apicultores</a>
                <a href="#contacto" @click="mobileMenuOpen = false" class="block py-2 hover:bg-yellow-100 rounded">Contacto</a>
                <a href="{{ route('login') }}" class="block py-2 mt-2 bg-[#CFCFCF] border border-gray-400 rounded-lg shadow-sm">Ingresar</a>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO DE LA PÁGINA-->
    <main class="flex-1 flex flex-col w-full h-full overflow-y-auto overflow-x-hidden relative">
        {{ $slot }}
    </main>

    <x-toast />
    <x-network-status /> 
</body>
</html>