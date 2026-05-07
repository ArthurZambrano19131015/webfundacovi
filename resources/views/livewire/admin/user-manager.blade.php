<div x-data="{
    showModal: false,
    isEdit: false,
    isOnline: navigator.onLine, // 1. Agregamos el estado de red
    photoPreview: null,
    form: { id: null, nombre_completo: '', email: '', telefono: '', id_rol: '', password: '' },

    openCreate() {
        this.isEdit = false;
        this.photoPreview = null;
        this.form = { id: null, nombre_completo: '', email: '', telefono: '', id_rol: '', password: '' };
        this.showModal = true;
    },

    openEdit(user) {
        this.isEdit = true;
        this.photoPreview = user.foto ? '/storage/' + user.foto : null;
        this.form = {
            id: user.id,
            nombre_completo: user.nombre_completo,
            email: user.email,
            telefono: user.telefono,
            id_rol: user.id_rol,
            password: ''
        };
        this.showModal = true;
    },

    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.photoPreview = URL.createObjectURL(file);
        }
    },

    async submit() {
        // 2. REGLA ESTRICTA: Bloqueo de seguridad si no hay red
        if (!this.isOnline) {
            this.$dispatch('notify', { message: 'Operación denegada. Este módulo requiere conexión al servidor.', type: 'error' });
            return;
        }

        try {
            let response;
            if (this.isEdit) {
                response = await $wire.updateUser(this.form);
            } else {
                response = await $wire.storeUser(this.form);
            }

            if (response && response.error) {
                this.$dispatch('notify', { message: response.error, type: 'error' });
                return;
            }

            this.$dispatch('notify', { message: 'Usuario guardado exitosamente', type: 'success' });
            this.showModal = false;

        } catch (e) {
            this.$dispatch('notify', { message: 'Fallo de conexión al guardar.', type: 'warning' });
        }
    }
}" @online.window="isOnline = true" @offline.window="isOnline = false">

    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h2>
        <button @click="openCreate()"
            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow w-full sm:w-auto">
            + Nuevo Usuario
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($usuarios as $user)
            <div
                class="bg-white rounded-2xl shadow-lg border border-yellow-100 overflow-hidden flex flex-col transition hover:shadow-xl relative">

                <!-- Badge de Estado -->
                <div
                    class="absolute top-4 right-4 px-2 py-1 rounded-full text-xs font-bold shadow-sm {{ $user->estado_activo ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ $user->estado_activo ? 'Activo' : 'Inactivo' }}
                </div>

                <!-- Cabecera y Foto -->
                <div class="bg-yellow-200 hover:bg-yellow-400 h-24 w-full"></div>
                <div class="flex justify-center -mt-12">
                    <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nombre_completo) . '&color=7F9CF5&background=EBF4FF' }}"
                        alt="Foto Perfil" class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover">
                </div>

                <!-- Información -->
                <div class="p-6 flex-1 flex flex-col items-center text-center">
                    <h3 class="text-xl font-bold text-gray-800">{{ $user->nombre_completo }}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{ $user->email }}</p>
                    <span
                        class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-semibold uppercase tracking-wider">
                        {{ $user->role->nombre_rol }}
                    </span>

                    @if ($user->telefono)
                        <p class="text-xs text-gray-400 mt-3 flex items-center gap-1">
                            📞 {{ $user->telefono }}
                        </p>
                    @endif
                </div>

                <!-- Acciones -->
                <div class="bg-gray-50 border-t border-gray-100 grid grid-cols-2 text-sm font-bold">
                    <button @click="openEdit({{ $user->toJson() }})"
                        class="p-3 text-blue-600 hover:bg-blue-50 transition border-r border-gray-100">
                        ✏️ Editar
                    </button>
                    <button wire:click="toggleStatus({{ $user->id }})"
                        wire:confirm="¿Deseas cambiar el estado de este usuario?"
                        class="p-3 transition {{ $user->estado_activo ? 'text-red-500 hover:bg-red-50' : 'text-green-500 hover:bg-green-50' }}">
                        {{ $user->estado_activo ? '🚫 Deshabilitar' : '✅ Habilitar' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal con Input de Foto -->
    <div wire:ignore.self x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
        style="display: none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden max-h-[90vh] flex flex-col"
            @click.away="showModal = false">
            <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Editar Usuario' : 'Registrar Usuario'">
                </h3>
                <button @click="showModal = false"
                    class="text-gray-600 hover:text-gray-900 font-bold text-2xl">&times;</button>
            </div>

            <form @submit.prevent="submit()" class="p-6 space-y-4 overflow-y-auto">

                <!-- AVISO DE SEGURIDAD OFFLINE -->
                <template x-if="!isOnline">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-4 flex items-start gap-3">
                        <span class="text-xl">📡</span>
                        <div>
                            <h4 class="text-sm font-bold text-red-800">Conexión Requerida</h4>
                            <p class="text-xs text-red-600 mt-1">Por políticas de seguridad y encriptación, no se
                                permite crear o editar credenciales de usuarios en modo Offline.</p>
                        </div>
                    </div>
                </template>

                <!-- PREVIEW Y SUBIDA DE FOTO -->
                <div class="flex flex-col items-center gap-2"
                    :class="!isOnline ? 'opacity-50 pointer-events-none' : ''">
                    <div
                        class="w-20 h-20 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!photoPreview">
                            <span class="text-gray-400 text-3xl">📷</span>
                        </template>
                    </div>
                    <input type="file" wire:model="foto" accept="image/*" @change="previewImage($event)"
                        class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                    @error('foto')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    <p class="text-sm text-yellow-500">La foto debe ser en formato JPG, PNG. max 2MB</p>
                </div>

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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" x-model="form.telefono"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                        <select x-model="form.id_rol"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Seleccione...</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contraseña <span x-show="isEdit"
                            class="text-xs text-gray-400">(Opcional)</span></label>
                    <input type="password" x-model="form.password"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    <p class="text-sm text-yellow-500">La contraseña debe tener al menos 8 caracteres y contener al
                        menos una letra mayúscula, una letra minúscula y un número.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" :disabled="!isOnline"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
