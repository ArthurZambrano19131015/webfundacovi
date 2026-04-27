<div x-data="{
    showModal: false,
    isEdit: false,
    isOnline: navigator.onLine,
    form: { id_local: '', id_apiario: '', identificador: '', tipo_colmena: '', fecha_instalacion: '' },

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

    openEdit(colmena) {
        this.isEdit = true;
        this.form = {
            id_local: colmena.id_local,
            id_apiario: colmena.id_apiario,
            identificador: colmena.identificador,
            tipo_colmena: colmena.tipo_colmena || '',
            // Ajustamos la fecha para que el input type='date' la entienda (YYYY-MM-DD)
            fecha_instalacion: colmena.fecha_instalacion.split('T')[0]
        };
        this.showModal = true;
    },

    async save() {
        if (!this.form.id_apiario || !this.form.identificador) {
            this.$dispatch('notify', { message: 'El Apiario y el Identificador son obligatorios', type: 'error' });
            return;
        }

        const idLocal = this.isEdit ? this.form.id_local : this.generateUUID();
        const payload = { ...this.form, id_local: idLocal, estado_activo: true };

        if (this.isOnline) {
            try {
                let response;
                if (this.isEdit) {
                    response = await $wire.updateColmena(payload);
                } else {
                    response = await $wire.storeColmena(payload);
                }

                if (response && response.error) {
                    this.$dispatch('notify', { message: response.error, type: 'error' });
                    return; // Detenemos la ejecución aquí, NO cerramos el modal
                }

                this.$dispatch('notify', { message: 'Colmena guardada correctamente', type: 'success' });
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
            if (!window.db) throw new Error('Base de datos local inactiva');
            const offlineData = { ...payload, synced: 0, created_at: new Date().toISOString() };
            // put actualizará si el id_local ya existe, o lo creará si es nuevo
            await window.db.colmenas.put(offlineData);
            this.showModal = false;
            this.$dispatch('notify', { message: 'Guardado en dispositivo (Modo Offline)', type: 'info' });
        } catch (e) {
            this.$dispatch('notify', { message: 'Error en almacenamiento local: ' + e.message, type: 'error' });
        }
    },

    resetForm() {
        this.form = { id_local: '', id_apiario: '', identificador: '', tipo_colmena: '', fecha_instalacion: '' };
    }
}" @online.window="isOnline = true" @offline.window="isOnline = false">

    <!-- HEADER Y FILTRO -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-yellow-300 pb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Gestión de Colmenas</h2>
            @if($isAdmin)
                <p class="text-sm font-bold text-blue-600">Vista de Administrador (Todas las colmenas)</p>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            
            <x-filter-menu :hasActiveFilters="$hasFilters">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Buscar (ID o Tipo)</label>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Ej: COL-001..." class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Apiario</label>
                    <select wire:model.live="filtro_apiario" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                        <option value="">Todos los apiarios</option>
                        @foreach($apiarios as $api) <option value="{{ $api->id }}">{{ $api->nombre }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Estado</label>
                    <select wire:model.live="filtro_estado" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
            </x-filter-menu>

            <button @click="openCreate()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition whitespace-nowrap">
                + Nueva Colmena
            </button>
        </div>
    </div>

    <!-- VISTA MÓVIL: Tarjetas -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($colmenas as $colmena)
            <div
                class="bg-white rounded-xl shadow-md p-5 border-l-4 {{ $colmena->estado_activo ? 'border-yellow-400' : 'border-gray-400' }}">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="font-black text-xl text-gray-800">{{ $colmena->identificador }}</h3>
                    <span
                        class="px-2 py-1 text-xs font-bold rounded-full {{ $colmena->estado_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $colmena->estado_activo ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>

                <div class="text-sm text-gray-600 space-y-1 mb-4 mt-2">
                    <p><strong>Apiario:</strong> {{ $colmena->apiario->nombre }}</p>
                    @if ($isAdmin)
                        <p class="text-xs text-blue-600">Productor: {{ $colmena->apiario->apicultor->nombre_completo }}
                        </p>
                    @endif
                    <p><strong>Tipo:</strong> {{ $colmena->tipo_colmena ?? 'Estándar' }}</p>
                    <p><strong>Instalación:</strong>
                        {{ \Carbon\Carbon::parse($colmena->fecha_instalacion)->format('d/m/Y') }}</p>
                </div>

                <div class="grid grid-cols-2 gap-2 border-t pt-3">
                    <button @click="openEdit({{ $colmena->toJson() }})"
                        class="w-full py-2 text-sm font-bold rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50">
                        ✏️ Editar
                    </button>
                    <button wire:click="toggleColmenaStatus({{ $colmena->id }})"
                        class="w-full py-2 text-sm font-bold rounded-lg border {{ $colmena->estado_activo ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                        {{ $colmena->estado_activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 p-6 bg-white rounded-xl shadow">No se encontraron colmenas.</div>
        @endforelse
    </div>

    <!-- VISTA DESKTOP: Tabla -->
    <div class="hidden md:block bg-white shadow-xl rounded-xl overflow-hidden border border-yellow-200">
        <table class="min-w-full">
            <thead class="bg-yellow-400 text-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">ID Colmena</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Apiario</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Tipo</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Fecha Inst.</th>
                    <th class="px-6 py-3 text-center font-bold uppercase text-xs">Estado</th>
                    <th class="px-6 py-3 text-center font-bold uppercase text-xs">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($colmenas as $colmena)
                    <tr
                        class="{{ $colmena->estado_activo ? 'hover:bg-yellow-50' : 'bg-gray-50 opacity-60' }} transition">
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $colmena->identificador }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $colmena->apiario->nombre }}
                            @if ($isAdmin)
                                <br><span
                                    class="text-xs font-bold text-blue-500">{{ $colmena->apiario->apicultor->nombre_completo }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $colmena->tipo_colmena ?? 'Estándar' }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($colmena->fecha_instalacion)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-2 inline-flex text-xs font-semibold rounded-full {{ $colmena->estado_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $colmena->estado_activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button @click="openEdit({{ $colmena->toJson() }})"
                                class="mr-3 text-sm font-semibold text-blue-600 hover:text-blue-800">Editar</button>
                            <button wire:click="toggleColmenaStatus({{ $colmena->id }})"
                                class="text-sm font-semibold {{ $colmena->estado_activo ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }}">
                                {{ $colmena->estado_activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No se encontraron
                            colmenas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-6">
        {{ $colmenas->links() }}
    </div>

    <!-- MODAL -->
    <div wire:ignore.self x-show="showModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
        style="display: none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showModal = false">
            <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800"
                    x-text="isEdit ? 'Editar Colmena' : 'Registrar Nueva Colmena'"></h3>
                <button @click="showModal = false"
                    class="text-gray-600 hover:text-gray-900 font-bold text-2xl">&times;</button>
            </div>
            <form @submit.prevent="save()" class="p-6 space-y-4">

                <!-- SELECCIÓN DE APIARIO -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Apiario Origen</label>
                    <select x-model="form.id_apiario" required
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Seleccione un apiario...</option>
                        @foreach ($apiarios as $apiario)
                            <option value="{{ $apiario->id }}">
                                {{ $apiario->nombre }} @if ($isAdmin)
                                    ({{ $apiario->apicultor->nombre_completo }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Identificador de Colmena (ID)</label>
                    <input type="text" x-model="form.identificador" placeholder="Ej: COL-001" required
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de Colmena</label>
                    <input type="text" x-model="form.tipo_colmena" placeholder="Ej: Langstroth"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Instalación</label>
                    <input type="date" x-model="form.fecha_instalacion" required
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl shadow-lg transition"
                        x-text="isEdit ? 'Guardar Cambios' : 'Registrar Colmena'">
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
