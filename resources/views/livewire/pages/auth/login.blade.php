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
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-400 mb-1">Contraseña</label>
                    <input wire:model="form.password" id="password" type="password" required
                        autocomplete="current-password"
                        class="w-full bg-[#222] border border-gray-700 rounded-md text-white px-4 py-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
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
