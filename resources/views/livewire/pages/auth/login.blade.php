<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex-1 flex w-full h-full">

    <!-- Imagen de Fondo Izquierda -->
    <div class="hidden md:block md:w-2/3 bg-cover bg-center relative"
        style="background-image: url('{{ asset('img/fondo_login.png') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-30 flex items-end p-12">
            <h1 class="text-white text-5xl font-bold drop-shadow-lg">Control apícola en la región <br>de Norte de
                Santander</h1>
        </div>
    </div>

    <!-- Contenedor del Formulario -->
    <div class="w-full md:w-1/3 bg-[#111111] flex flex-col justify-center items-center p-8 shadow-2xl relative">
        <div class="w-full max-w-sm mx-auto">
            <h2 class="text-3xl font-semibold text-white text-center mb-8">Bienvenido</h2>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-400 mb-1">Email</label>
                    <input wire:model="form.email" id="email" type="email" required autofocus
                        autocomplete="username"
                        class="w-full bg-[#222] border border-gray-700 rounded-md text-white px-4 py-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Password -->
                <!-- Password con botón de Ojo (Alpine.js) -->
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-medium text-gray-400 mb-1">Contraseña</label>
                    <div class="relative">
                        <!-- El atributo :type cambia dinámicamente según el estado de showPassword -->
                        <input wire:model="form.password" id="password" :type="showPassword ? 'text' : 'password'"
                            required autocomplete="current-password"
                            class="w-full bg-[#222] border border-gray-700 rounded-md text-white pl-4 pr-10 py-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-300 focus:outline-none transition">
                            <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox"
                            class="rounded border-gray-700 bg-[#222] text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ms-2 text-sm text-gray-400">Recordarme</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-400 hover:text-white transition"
                            href="{{ route('password.request') }}" wire:navigate>
                            ¿Has olvidado tu contraseña?
                        </a>
                    @endif
                </div>

                <!-- Botones -->
                <div class="mt-6 flex flex-col gap-3">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        Iniciar sesión
                    </button>

                    <!-- 2. CAMBIO IMPORTANTE:-->
                    <a href="/"
                        class="w-full text-center border border-gray-600 hover:bg-gray-800 text-gray-300 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out text-sm">
                        &larr; Volver al inicio
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
