<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-red-600">
            Eliminar Cuenta
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Una vez que se elimine tu cuenta, todos sus datos y recursos se borrarán de forma permanente.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
        Eliminar Cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">
            <h2 class="text-lg font-bold text-gray-900">
                ¿Estás seguro de que deseas eliminar tu cuenta?
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Esta acción no se puede deshacer. Por favor, ingresa tu contraseña para confirmar.
            </p>

            <div class="mt-6">
                <label class="sr-only">Contraseña</label>
                <input wire:model="password" type="password" placeholder="Contraseña"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 shadow-md transition">
                    Eliminar Permanentemente
                </button>
            </div>
        </form>
    </x-modal>
</section>
