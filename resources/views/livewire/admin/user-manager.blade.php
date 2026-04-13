<div x-data="{
    showModal: false,
    form: { nombre_completo: '', email: '', telefono: '', id_rol: '', password: '' },
    
    submit() {
        // Llamamos al método de Livewire pasando el objeto reactivo de Alpine
        $wire.storeUser(this.form).then(() => {
            this.showModal = false;
            this.form = { nombre_completo: '', email: '', telefono: '', id_rol: '', password: '' };
        });
    }
}">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Gestión de Usuarios</h2>
        <button @click="showModal = true" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
            + Nuevo Usuario
        </button>
    </div>

    <!-- Tabla de Usuarios (RF6) -->
    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">Nombre Completo</th>
                <th class="px-4 py-2">Rol</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $user)
            <tr class="border-b">
                <td class="px-4 py-2">{{ $user->nombre_completo }}</td>
                <td class="px-4 py-2">{{ $user->role->nombre_rol }}</td>
                <td class="px-4 py-2">
                    <span class="{{ $user->estado_activo ? 'text-green-600' : 'text-red-600' }}">
                        {{ $user->estado_activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="px-4 py-2">
                    <!-- Botón Deshabilitar (RF8) -->
                    <button wire:click="toggleStatus({{ $user->id }})" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ $user->estado_activo ? 'Deshabilitar' : 'Habilitar' }}
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal Alpine.js (RF5) -->
    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h3 class="text-lg font-bold mb-4">Registrar Nuevo Usuario</h3>
            
            <!-- Usamos x-model en lugar de wire:model -->
            <input type="text" x-model="form.nombre_completo" placeholder="Nombre Completo" class="w-full mb-3 border rounded p-2">
            <input type="email" x-model="form.email" placeholder="Correo Electrónico" class="w-full mb-3 border rounded p-2">
            <input type="text" x-model="form.telefono" placeholder="Teléfono" class="w-full mb-3 border rounded p-2">
            
            <select x-model="form.id_rol" class="w-full mb-3 border rounded p-2">
                <option value="">Seleccione un Rol...</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                @endforeach
            </select>

            <input type="password" x-model="form.password" placeholder="Contraseña Temporal" class="w-full mb-4 border rounded p-2">

            <div class="flex justify-end gap-2">
                <button @click="showModal = false" class="text-gray-500">Cancelar</button>
                <button @click="submit()" class="bg-yellow-500 text-white px-4 py-2 rounded">Guardar</button>
            </div>
        </div>
    </div>
</div>