<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<!-- Menú desplegable impulsado 100% por Alpine.js -->
<div x-data="{ open: false }" class="relative">

    <!-- Botón con el nombre del usuario -->
    <button @click="open = !open" @click.away="open = false"
        class="flex items-center gap-2 font-medium text-gray-800 hover:text-yellow-700 transition focus:outline-none">
        <span>{{ auth()->user()->nombre_completo }}</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Caja del Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5"
        style="display: none;">

        <a href="{{ route('profile') }}"
            class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition">
            Mi Perfil
        </a>

        <!-- Botón de Cerrar Sesión (Ejecuta la función PHP de arriba) -->
        <button wire:click="logout"
            class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
            Cerrar Sesión
        </button>
    </div>
</div>
