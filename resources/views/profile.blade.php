<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto space-y-6">
            
            <!-- Tarjeta: Información del Perfil -->
            <div class="p-6 sm:p-8 bg-white shadow-lg rounded-2xl border border-yellow-200">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Tarjeta: Actualizar Contraseña -->
            <div class="p-6 sm:p-8 bg-white shadow-lg rounded-2xl border border-yellow-200">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
