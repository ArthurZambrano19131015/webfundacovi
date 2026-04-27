<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component {
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::broker()->sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            // Disparamos el Toast en lugar del mensaje nativo
            $this->dispatch('notify', message: __($status), type: 'success');
            $this->reset('email');
        } else {
            $this->dispatch('notify', message: __($status), type: 'error');
            $this->addError('email', __($status));
        }
    }
}; ?>

<div class="flex-1 flex w-full h-full">

    <!-- Imagen de Fondo Izquierda -->
    <div class="hidden md:block md:w-2/3 bg-cover bg-center relative"
        style="background-image: url('{{ asset('img/fondo_login.png') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-end p-12">
            <h1 class="text-white text-5xl font-black drop-shadow-lg leading-tight">Control apícola en la región <br>de
                Norte de Santander</h1>
        </div>
    </div>

    <!-- Contenedor del Formulario Oscuro (Derecha) -->
    <div class="w-full md:w-1/3 bg-[#111111] flex flex-col justify-center items-center p-8 shadow-2xl relative">
        <div class="w-full max-w-sm mx-auto">
            <h2 class="text-3xl font-black text-white text-center mb-4">Recuperar Acceso</h2>

            <div class="mb-8 text-sm text-gray-400 text-center leading-relaxed">
                ¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para
                que elijas una nueva.
            </div>

            <form wire:submit="sendPasswordResetLink" class="space-y-6">
                <!-- Correo Electrónico -->
                <div>
                    <label for="email"
                        class="block text-sm font-bold text-gray-400 mb-1 uppercase tracking-wider">Correo
                        Electrónico</label>
                    <input wire:model="email" id="email" type="email" required autofocus
                        class="w-full bg-[#222] border border-gray-700 rounded-md text-white px-4 py-3 focus:ring-blue-500 focus:border-blue-500 focus:outline-none shadow-inner">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-bold" />
                </div>

                <div class="flex flex-col gap-3 mt-6">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-wider py-3 px-4 rounded-md transition duration-150 ease-in-out shadow-lg flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="sendPasswordResetLink">Enviar Enlace</span>
                        <span wire:loading wire:target="sendPasswordResetLink">Enviando Correo... ⏳</span>
                    </button>

                    <a href="{{ route('login') }}"
                        class="w-full text-center border border-gray-700 hover:bg-gray-800 text-gray-400 hover:text-white font-bold py-3 px-4 rounded-md transition duration-150 ease-in-out text-sm">
                        &larr; Volver al Login
                    </a>
                    <a href="/"
                        class="w-full text-center border border-gray-600 hover:bg-gray-800 text-gray-300 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out text-sm">
                        &larr; Volver al inicio
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
