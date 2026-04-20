<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-xl font-bold text-gray-800">
            Actualizar Contraseña
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Asegúrate de que tu cuenta use una contraseña larga y segura.
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
            <input wire:model="current_password" type="password" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
            <input wire:model="password" type="password" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
            <input wire:model="password_confirmation" type="password" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <!-- Botón Amarillo -->
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                Guardar Contraseña
            </button>

            <x-action-message class="text-green-600 font-bold" on="password-updated">
                ¡Actualizada!
            </x-action-message>
        </div>
    </form>
</section>