<div x-data="{
    showModal: false,
    isEdit: false,
    form: {
        id: null,
        nombre_completo: '',
        email: '',
        telefono: '',
        id_rol: '',
        password: ''
    },

    openCreate() {
        this.isEdit = false;
        this.form = { id: null, nombre_completo: '', email: '', telefono: '', id_rol: '', password: '' };
        this.showModal = true;
    },

    openEdit(user) {
        this.isEdit = true;
        this.form = {
            id: user.id,
            nombre_completo: user.nombre_completo,
            email: user.email,
            telefono: user.telefono,
            id_rol: user.id_rol,
            password: '' // Vacio por seguridad
        };
        this.showModal = true;
    },

    submit() {
        if (this.isEdit) {
            // Livewire method: updateUser
            $wire.updateUser(this.form).then(() => { this.showModal = false; });
        } else {
            // Livewire method: storeUser
            $wire.storeUser(this.form).then(() => { this.showModal = false; });
        }
    }
}">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h2>
        <button @click="openCreate()"
            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow">
            + Nuevo Usuario
        </button>
    </div>

    <!-- Tabla de Usuarios con estilo Figma -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-yellow-200">
        <table class="min-w-full">
            <thead class="bg-yellow-400 text-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left font-bold uppercase text-sm">Nombre Completo</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-sm">Rol</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-sm">Estado</th>
                    <th class="px-6 py-3 text-center font-bold uppercase text-sm">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($usuarios as $user)
                    <tr class="hover:bg-yellow-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->nombre_completo }} <br> <span
                                class="text-xs text-gray-500">{{ $user->email }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap"><span
                                class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">{{ $user->role->nombre_rol }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->estado_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->estado_activo ? 'Activo' : 'Deshabilitado' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button @click="openEdit({{ $user->toJson() }})"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>

                            <button wire:click="toggleStatus({{ $user->id }})"
                                wire:confirm="¿Seguro que deseas cambiar el estado de este usuario?"
                                class="{{ $user->estado_activo ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                {{ $user->estado_activo ? 'Deshabilitar' : 'Habilitar' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
        style="display: none;" x-transition>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showModal = false">
            <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800"
                    x-text="isEdit ? 'Editar Usuario' : 'Registrar Nuevo Usuario'"></h3>
                <button @click="showModal = false"
                    class="text-gray-600 hover:text-gray-900 font-bold text-xl">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                    <input type="text" x-model="form.nombre_completo"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input type="email" x-model="form.email"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" x-model="form.telefono"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Rol del Sistema</label>
                    <select x-model="form.id_rol"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Seleccione un Rol...</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contraseña <span x-show="isEdit"
                            class="text-xs text-gray-400">(Dejar en blanco para mantener actual)</span></label>
                    <input type="password" x-model="form.password"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                <button @click="showModal = false"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300">Cancelar</button>
                <button @click="submit()"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg font-bold hover:bg-yellow-600 shadow-md">Guardar
                    Datos</button>
            </div>
        </div>
    </div>
</div>
