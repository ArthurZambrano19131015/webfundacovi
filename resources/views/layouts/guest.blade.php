<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FUNDACOVI - Pro-Productos Apícolas</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased h-screen flex flex-col bg-[#F3EAC0]">
        
        <nav class="bg-[#E9EFA7] px-6 py-3 flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-4">
                <!-- Logo local -->
                <img src="{{ asset('img/logo.png') }}" alt="Logo Apicultura" class="h-12 w-12 rounded-xl shadow-md object-cover">
                <span class="font-black text-2xl text-black tracking-wide">Apicultura</span>
            </div>

            <!-- Enlaces centrales -->
            <div class="hidden md:flex items-center gap-6 font-bold text-black">
                <a href="/" class="hover:text-gray-700">Inicio</a>
                <a href="#" class="border-2 border-gray-600 rounded-full px-4 py-1 hover:bg-gray-200 transition">Productos</a>
                <a href="#" class="hover:text-gray-700">Apicultores</a>
                <a href="#" class="hover:text-gray-700">Contacto</a>
            </div>

            <!-- Botón Ingresar -->
            <div class="hidden md:flex items-center">
                <a href="{{ route('login') }}" class="bg-[#CFCFCF] text-black font-bold px-6 py-2 rounded-full border border-gray-400 shadow-sm hover:bg-gray-300 transition">
                    Ingresar
                </a>
            </div>
        </nav>

        <!-- CONTENIDO DE LA PÁGINA  -->
        <main class="flex-1 flex flex-col w-full h-full">
            {{ $slot }}
        </main>

    </body>
</html>