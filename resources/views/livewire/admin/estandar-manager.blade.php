<div x-data="{
    showModal: false,
    isEdit: false,
    isOnline: navigator.onLine,
    form: { id_local: '', parametro: '', valor_minimo: '', valor_maximo: '', unidad_medida: '' },

    generateUUID() {
        if (window.crypto && window.crypto.randomUUID) return window.crypto.randomUUID();
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    openCreate() {
        this.isEdit = false;
        this.resetForm();
        this.showModal = true;
    },

    openEdit(estandar) {
        this.isEdit = true;
        this.form = {
            id_local: estandar.id_local,
            parametro: estandar.parametro,
            valor_minimo: estandar.valor_minimo || '',
            valor_maximo: estandar.valor_maximo || '',
            unidad_medida: estandar.unidad_medida
        };
        this.showModal = true;
    },

    async save() {
        if (!this.form.parametro || !this.form.unidad_medida) {
            this.$dispatch('notify', { message: 'Parámetro y Unidad son obligatorios', type: 'error' });
            return;
        }

        const idLocal = this.isEdit ? this.form.id_local : this.generateUUID();
        const payload = { ...this.form, id_local: idLocal, estado_activo: true };

        if (this.isOnline) {
            try {
                let response = this.isEdit ? await $wire.updateEstandar(payload) : await $wire.storeEstandar(payload);
                
                if (response && response.error) {
                    this.$dispatch('notify', { message: response.error, type: 'error' });
                    return;
                }

                this.$dispatch('notify', { message: 'Estándar guardado con éxito', type: 'success' });
                this.showModal = false;
                this.resetForm();
            } catch (e) {
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
            await window.db.estandares.put(offlineData);
            this.showModal = false;
            this.resetForm();
            this.$dispatch('notify', { message: 'Estándar guardado localmente (Offline)', type: 'info' });
        } catch (e) {
            this.$dispatch('notify', { message: 'Error local: ' + e.message, type: 'error' });
        }
    },

    resetForm() {
        this.form = { id_local: '', parametro: '', valor_minimo: '', valor_maximo: '', unidad_medida: '' };
    }
}" 
@online.window="isOnline = true" 
@offline.window="isOnline = false">

    <!-- Header y Buscador -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Estándares de Calidad</h2>
            <p class="text-sm font-bold text-blue-600">Configuración Normativa (INVIMA)</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar parámetro..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full md:w-72 focus:ring-yellow-500 focus:border-yellow-500">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <button @click="openCreate()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition whitespace-nowrap">
                + Nuevo Estándar
            </button>
        </div>
    </div>

    <!-- VISTA MÓVIL: Tarjetas -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($estandares as $est)
        <div class="bg-white rounded-xl shadow-md p-5 border-l-4 {{ $est->estado_activo ? 'border-yellow-400' : 'border-gray-400' }}">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-black text-xl text-gray-800">{{ $est->parametro }}</h3>
                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $est->estado_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $est->estado_activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            
            <div class="bg-yellow-50 p-3 rounded-lg mb-4 text-sm text-gray-700">
                <p><strong>Mínimo:</strong> {{ $est->valor_minimo ?? 'N/A' }} {{ $est->unidad_medida }}</p>
                <p><strong>Máximo:</strong> {{ $est->valor_maximo ?? 'N/A' }} {{ $est->unidad_medida }}</p>
            </div>

            <div class="grid grid-cols-2 gap-2 border-t pt-3">
                <button @click="openEdit({{ $est->toJson() }})" class="w-full py-2 text-sm font-bold rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50">✏️ Editar</button>
                <button wire:click="toggleStatus({{ $est->id }})" class="w-full py-2 text-sm font-bold rounded-lg border {{ $est->estado_activo ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                    {{ $est->estado_activo ? 'Desactivar' : 'Activar' }}
                </button>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 p-6 bg-white rounded-xl shadow">No hay estándares configurados.</div>
        @endforelse
    </div>

    <!-- VISTA DESKTOP: Tabla -->
    <div class="hidden md:block bg-white shadow-xl rounded-xl overflow-hidden border border-yellow-200">
        <table class="min-w-full">
            <thead class="bg-yellow-400 text-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Parámetro</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Valor Mín.</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Valor Máx.</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Unidad</th>
                    <th class="px-6 py-3 text-center font-bold uppercase text-xs">Estado</th>
                    <th class="px-6 py-3 text-center font-bold uppercase text-xs">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($estandares as $est)
                <tr class="{{ $est->estado_activo ? 'hover:bg-yellow-50' : 'bg-gray-50 opacity-60' }} transition">
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $est->parametro }}</td>
                    <td class="px-6 py-4 font-medium text-gray-600">{{ $est->valor_minimo ?? 'No aplica' }}</td>
                    <td class="px-6 py-4 font-medium text-gray-600">{{ $est->valor_maximo ?? 'No aplica' }}</td>
                    <td class="px-6 py-4 text-gray-600 font-bold">{{ $est->unidad_medida }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 inline-flex text-xs font-semibold rounded-full {{ $est->estado_activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $est->estado_activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button @click="openEdit({{ $est->toJson() }})" class="mr-3 text-sm font-semibold text-blue-600 hover:text-blue-800">Editar</button>
                        <button wire:click="toggleStatus({{ $est->id }})" class="text-sm font-semibold {{ $est->estado_activo ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }}">
                            {{ $est->estado_activo ? 'Desactivar' : 'Activar' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No hay estándares configurados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-6">
        {{ $estandares->links() }}
    </div>

    <!-- MODAL -->
    <div wire:ignore.self x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showModal = false">
            <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Editar Estándar' : 'Nuevo Estándar'"></h3>
                <button @click="showModal = false" class="text-gray-600 hover:text-gray-900 font-bold text-2xl">&times;</button>
            </div>
            <form @submit.prevent="save()" class="p-6 space-y-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Parámetro</label>
                    <input type="text" x-model="form.parametro" placeholder="Ej: Humedad" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Valor Mínimo (Opcional)</label>
                        <input type="number" step="0.01" x-model="form.valor_minimo" placeholder="0.00" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Valor Máximo (Opcional)</label>
                        <input type="number" step="0.01" x-model="form.valor_maximo" placeholder="0.00" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                    <input type="text" x-model="form.unidad_medida" placeholder="Ej: % o mg/kg" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl shadow-lg transition" x-text="isEdit ? 'Guardar Cambios' : 'Registrar Estándar'">
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>