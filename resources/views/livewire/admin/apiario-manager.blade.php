<div x-data="{
    showModal: false,
    isEdit: false,
    isOnline: navigator.onLine,
    form: { id_local: '', nombre: '', latitud: '', longitud: '', municipio: '' },

    generateUUID() {
        if (window.crypto && window.crypto.randomUUID) return window.crypto.randomUUID();
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    openCreate() {
        this.isEdit = false;
        this.resetForm();
        this.showModal = true;
    },

    openEdit(apiario) {
        this.isEdit = true;
        this.form = {
            id_local: apiario.id_local,
            nombre: apiario.nombre,
            latitud: apiario.latitud || '',
            longitud: apiario.longitud || '',
            municipio: apiario.municipio || ''
        };
        this.showModal = true;
    },

    async save() {
        if (!this.form.nombre) {
            this.$dispatch('notify', { message: 'El nombre del apiario es obligatorio', type: 'error' });
            return;
        }

        // Si es edición usamos su ID, si es nuevo generamos uno
        const idLocal = this.isEdit ? this.form.id_local : this.generateUUID();
        const payload = { ...this.form, id_local: idLocal, estado_activo: true };

        if (this.isOnline) {
            try {
                if (this.isEdit) {
                    await $wire.updateApiario(payload);
                    this.$dispatch('notify', { message: 'Apiario actualizado en servidor', type: 'success' });
                } else {
                    await $wire.storeApiario(payload);
                    this.$dispatch('notify', { message: 'Apiario registrado en servidor', type: 'success' });
                }
                this.showModal = false;
                this.resetForm();
            } catch (e) {
                console.error(e);
                this.$dispatch('notify', { message: 'Fallo de red, guardando localmente...', type: 'warning' });
                this.saveLocal(payload);
            }
        } else {
            this.saveLocal(payload);
        }
    },

    async saveLocal(payload) {
        try {
            if (!window.db) throw new Error('DB local no disponible');

            const offlineData = {
                ...payload,
                id_apicultor: {{ auth()->id() }},
                synced: 0,
                created_at: new Date().toISOString()
            };

            // Usamos put() para que cree o actualice basándose en el id_local
            await window.db.apiarios.put(offlineData);

            this.showModal = false;
            this.resetForm();
            this.$dispatch('notify', { message: 'Guardado en dispositivo (Modo Offline)', type: 'info' });
        } catch (e) {
            console.error(e);
            this.$dispatch('notify', { message: 'Error en almacenamiento local: ' + e.message, type: 'error' });
        }
    },

    resetForm() {
        this.form = { id_local: '', nombre: '', latitud: '', longitud: '', municipio: '' };
    }
}" @online.window="isOnline = true" @offline.window="isOnline = false">

    <!-- Header y Buscador -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Gestión de Apiarios</h2>
            @if ($isAdmin)
                <p class="text-sm font-bold text-blue-600">Vista de Administrador (Todos los apiarios)</p>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por nombre o municipio..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full md:w-72 focus:ring-yellow-500 focus:border-yellow-500">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <button @click="openCreate()"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition whitespace-nowrap">
                + Nuevo Apiario
            </button>
        </div>
    </div>

    <!-- VISTA MÓVIL: Tarjetas -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($apiarios as $apiario)
            <div
                class="bg-white rounded-xl shadow-md p-5 border-l-4 {{ $apiario->estado_activo ? 'border-green-500' : 'border-red-500' }} relative">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-lg text-gray-800">{{ $apiario->nombre }}</h3>
                    <span
                        class="px-2 py-1 text-xs font-bold rounded-full {{ $apiario->estado_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $apiario->estado_activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <p class="text-sm text-gray-600 mb-1">📍 {{ $apiario->municipio ?? 'Ubicación no registrada' }}</p>

                @if ($isAdmin)
                    <p class="text-sm text-blue-600 mb-4">👨‍🌾 Productor:
                        <strong>{{ $apiario->apicultor->nombre_completo }}</strong></p>
                @endif

                <div class="grid grid-cols-2 gap-2 border-t pt-3 mt-3">
                    <button @click="openEdit({{ $apiario->toJson() }})"
                        class="w-full py-2 text-sm font-bold rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition">
                        ✏️ Editar
                    </button>
                    <button wire:click="deleteApiario({{ $apiario->id }})"
                        class="w-full py-2 text-sm font-bold rounded-lg border transition {{ $apiario->estado_activo ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                        {{ $apiario->estado_activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 p-6 bg-white rounded-xl shadow">No se encontraron apiarios.</div>
        @endforelse
    </div>

    <!-- VISTA DESKTOP: Tabla -->
    <div class="hidden md:block bg-white shadow-xl rounded-xl overflow-hidden border border-yellow-200">
        <table class="min-w-full">
            <thead class="bg-yellow-400 text-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Nombre</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Municipio</th>
                    <th class="px-6 py-3 text-center font-bold uppercase text-xs">Estado</th>
                    <th class="px-6 py-3 text-center font-bold uppercase text-xs">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($apiarios as $apiario)
                    <tr
                        class="{{ $apiario->estado_activo ? 'hover:bg-yellow-50' : 'bg-gray-50 opacity-60' }} transition">
                        <td
                            class="px-6 py-4 font-medium {{ $apiario->estado_activo ? 'text-gray-800' : 'text-gray-400' }}">
                            {{ $apiario->nombre }}
                            @if ($isAdmin)
                                <br><span
                                    class="text-xs font-bold text-blue-500">{{ $apiario->apicultor->nombre_completo }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $apiario->municipio ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-2 inline-flex text-xs font-semibold rounded-full {{ $apiario->estado_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $apiario->estado_activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center flex justify-center gap-4">
                            <button @click="openEdit({{ $apiario->toJson() }})"
                                class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                                Editar
                            </button>
                            <button wire:click="deleteApiario({{ $apiario->id }})"
                                class="text-sm font-semibold transition {{ $apiario->estado_activo ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }}">
                                {{ $apiario->estado_activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No se encontraron
                            apiarios.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-6">
        {{ $apiarios->links() }}
    </div>

    <!-- MODAL -->
    <div wire:ignore.self x-show="showModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
        style="display: none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showModal = false">
            <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Editar Apiario' : 'Registrar Apiario'">
                </h3>
                <button @click="showModal = false"
                    class="text-gray-600 hover:text-gray-900 font-bold text-2xl">&times;</button>
            </div>

            <form @submit.prevent="save()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Apiario</label>
                    <input type="text" x-model="form.nombre" required
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Latitud</label>
                        <input type="text" x-model="form.latitud" placeholder="Ej: 7.9113"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Longitud</label>
                        <input type="text" x-model="form.longitud" placeholder="Ej: -72.4997"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Municipio</label>
                    <input type="text" x-model="form.municipio" placeholder="Ej: Cúcuta"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl shadow-lg transition"
                        x-text="isEdit ? 'Guardar Cambios' : 'Guardar Apiario'">
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
