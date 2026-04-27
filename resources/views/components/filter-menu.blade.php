@props(['hasActiveFilters' => false])

<div x-data="{ openFilters: false }" class="relative inline-block text-left w-full md:w-auto">

    <div class="flex gap-2 w-full">
        <!-- Botón Principal para abrir filtros -->
        <button @click="openFilters = !openFilters" @click.away="openFilters = false"
            class="w-full md:w-auto flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-2 px-4 rounded-lg shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                </path>
            </svg>
            Filtros
            <!-- Punto rojo si hay filtros activos -->
            @if ($hasActiveFilters)
                <span class="absolute top-1.5 right-1.5 md:relative md:top-auto md:right-auto flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
            @endif
        </button>

        <!-- Botón Limpiar (Solo visible si hay filtros activos) -->
        @if ($hasActiveFilters)
            <button wire:click="resetFilters"
                class="flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold py-2 px-3 rounded-lg shadow-sm transition"
                title="Limpiar filtros">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Panel Desplegable de Filtros -->
    <div x-show="openFilters" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 transform -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 md:left-0 md:right-auto mt-2 w-72 md:w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 p-4"
        style="display: none;" @click.stop> <!-- Evita que se cierre al hacer clic adentro -->

        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 pb-2 border-b">Opciones de Búsqueda
        </h3>

        <!-- Slot donde inyectaremos los inputs de Livewire -->
        <div class="space-y-4">
            {{ $slot }}
        </div>
    </div>
</div>
