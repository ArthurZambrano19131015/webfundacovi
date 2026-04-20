<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    use WithFileUploads;

    public string $nombre_completo = '';
    public string $email = '';
    public string $telefono = '';
    public $foto_nueva;

    public function mount(): void
    {
        $this->nombre_completo = Auth::user()->nombre_completo;
        $this->email = Auth::user()->email;
        $this->telefono = Auth::user()->telefono ?? '';
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'nombre_completo' =>['required', 'string', 'max:100'],
            'email' =>['required', 'string', 'lowercase', 'email', 'max:100', Rule::unique(User::class)->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'foto_nueva' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->fill([
            'nombre_completo' => $validated['nombre_completo'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Logica para reemplazar la foto
        if ($this->foto_nueva) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $this->foto_nueva->store('usuarios', 'public');
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->nombre_completo);
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Información del Perfil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Actualiza tu información personal, correo electrónico y foto de perfil.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        
        <!-- FOTO DE PERFIL -->
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-yellow-400 shadow-sm">
                @if ($foto_nueva)
                    <img src="{{ $foto_nueva->temporaryUrl() }}" class="w-full h-full object-cover">
                @else
                    <img src="{{ auth()->user()->foto ? asset('storage/'.auth()->user()->foto) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->nombre_completo).'&color=7F9CF5&background=EBF4FF' }}" class="w-full h-full object-cover">
                @endif
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                <input type="file" wire:model="foto_nueva" accept="image/*" class="mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                <x-input-error class="mt-2" :messages="$errors->get('foto_nueva')" />
            </div>
        </div>

        <div>
            <x-input-label for="nombre_completo" :value="__('Nombre Completo')" />
            <x-text-input wire:model="nombre_completo" id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('nombre_completo')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="telefono" :value="__('Teléfono')" />
            <x-text-input wire:model="telefono" id="telefono" name="telefono" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar Cambios') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Guardado.') }}
            </x-action-message>
        </div>
    </form>
</section>