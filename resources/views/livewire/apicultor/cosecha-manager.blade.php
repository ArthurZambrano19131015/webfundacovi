<div x-data="{
    showModal: false,
    showConfirm: false, 
    isOnline: navigator.onLine,
    form: { id_local: '', id_colmena: '', fecha_recoleccion: '', cantidad_kg: '', novedades: '' },

    generateUUID() {
        if (window.crypto && window.crypto.randomUUID) return window.crypto.randomUUID();
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    openCreate() {
        this.resetForm();
        this.form.fecha_recoleccion = new Date().toISOString().split('T')[0];
        this.showModal = true;
        this.showConfirm = false;
    },

    // Paso 1: Validar y mostrar confirmación
    requestSave() {
        if (!this.form.id_colmena || !this.form.cantidad_kg) {
            this.$dispatch('notify', { message: 'La colmena y los Kg extraídos son obligatorios', type: 'error' });
            return;
        }
        // Pasó la validación, mostramos la confirmación
        this.showConfirm = true;
    },

    // Paso 2: Ejecutar el guardado real
    async executeSave() {
        const idLocal = this.generateUUID();
        const payload = { ...this.form, id_local: idLocal };

        if (this.isOnline) {
            try {
                let response = await $wire.storeCosecha(payload);
                if (response && response.error) {
                    this.$dispatch('notify', { message: response.error, type: 'error' });
                    this.showConfirm = false;
                    return;
                }
                this.$dispatch('notify', { message: 'Cosecha registrada con éxito', type: 'success' });
                this.closeAll();
            } catch (e) {
                this.$dispatch('notify', { message: 'Fallo de red. Guardando localmente...', type: 'warning' });
                this.saveLocal(payload);
            }
        } else {
            this.saveLocal(payload);
        }
    },

    async saveLocal(payload) {
        try {
            if (!window.db) throw new Error('DB no disponible');
            const offlineData = { ...payload, synced: 0, created_at: new Date().toISOString() };
            await window.db.cosechas.put(offlineData);
            this.closeAll();
            this.$dispatch('notify', { message: 'Cosecha guardada en dispositivo (Offline)', type: 'info' });
        } catch (e) {
            this.$dispatch('notify', { message: 'Error local: ' + e.message, type: 'error' });
        }
    },

    closeAll() {
        this.showModal = false;
        this.showConfirm = false;
        this.resetForm();
    },

    resetForm() {
        this.form = { id_local: '', id_colmena: '', fecha_recoleccion: '', cantidad_kg: '', novedades: '' };
    }
}" 
@online.window="isOnline = true" 
@offline.window="isOnline = false">

    <!-- Header y Filtros -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Registro de Cosechas</h2>
            <p class="text-sm font-bold text-yellow-600">Historial de Producción</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
            <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow-sm border border-yellow-200">
                <span class="text-xs font-bold text-gray-500 uppercase">Desde:</span>
                <input wire:model.live="fecha_inicio" type="date" class="text-sm border-none bg-transparent focus:ring-0 p-0 text-gray-700">
                <span class="text-xs font-bold text-gray-500 uppercase border-l pl-2">Hasta:</span>
                <input wire:model.live="fecha_fin" type="date" class="text-sm border-none bg-transparent focus:ring-0 p-0 text-gray-700">
            </div>

            <button @click="openCreate()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition whitespace-nowrap w-full md:w-auto">
                + Registrar Cosecha
            </button>
        </div>
    </div>

    <!-- VISTA MÓVIL: Tarjetas -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($cosechas as $cosecha)
        <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-yellow-500">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-black text-xl text-gray-800">{{ $cosecha->cantidad_kg }} kg</h3>
                <span class="text-xs font-bold text-gray-500">
                    {{ \Carbon\Carbon::parse($cosecha->fecha_recoleccion)->format('d/m/Y') }}
                </span>
            </div>
            
            <div class="bg-yellow-50 p-3 rounded-lg mb-2 text-sm text-gray-700">
                <p><strong>Colmena:</strong> {{ $cosecha->colmena->identificador }}</p>
                <p><strong>Apiario:</strong> {{ $cosecha->colmena->apiario->nombre }}</p>
            </div>
            
            @if($cosecha->novedades)
                <p class="text-xs text-gray-600 italic">"{{ $cosecha->novedades }}"</p>
            @endif
        </div>
        @empty
        <div class="text-center text-gray-500 p-6 bg-white rounded-xl shadow">No has registrado cosechas.</div>
        @endforelse
    </div>

    <!-- VISTA DESKTOP: Tabla -->
    <div class="hidden md:block bg-white shadow-xl rounded-xl overflow-hidden border border-yellow-200">
        <table class="min-w-full">
            <thead class="bg-yellow-400 text-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Fecha</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Apiario > Colmena</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Producción (Kg)</th>
                    <th class="px-6 py-3 text-left font-bold uppercase text-xs">Novedades</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($cosechas as $cosecha)
                <tr class="hover:bg-yellow-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-700">
                        {{ \Carbon\Carbon::parse($cosecha->fecha_recoleccion)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <strong>{{ $cosecha->colmena->apiario->nombre }}</strong> <br>
                        <span class="text-xs">ID: {{ $cosecha->colmena->identificador }}</span>
                    </td>
                    <td class="px-6 py-4 font-black text-yellow-600 text-lg">
                        {{ $cosecha->cantidad_kg }} kg
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm italic">
                        {{ $cosecha->novedades ?? 'Sin novedades' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No has registrado cosechas aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $cosechas->links() }}</div>

    <!-- MODAL PRINCIPAL -->
    <div wire:ignore.self x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" style="display: none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative" @click.away="showConfirm ? null : closeAll()">
            
            <!-- CAPA 1: FORMULARIO -->
            <div x-show="!showConfirm" x-transition>
                <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Registrar Cosecha</h3>
                    <button @click="closeAll()" class="text-gray-600 hover:text-gray-900 font-bold text-2xl">&times;</button>
                </div>
                <form @submit.prevent="requestSave()" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Colmena Origen</label>
                        <select x-model="form.id_colmena" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Seleccione una colmena...</option>
                            @foreach($colmenas as $col)
                                <option value="{{ $col->id }}">
                                    {{ $col->apiario->nombre }} - {{ $col->identificador }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha</label>
                            <input type="date" x-model="form.fecha_recoleccion" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Producción (Kg)</label>
                            <input type="number" step="0.1" x-model="form.cantidad_kg" placeholder="0.0" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Novedades (Opcional)</label>
                        <textarea x-model="form.novedades" rows="3" placeholder="Ej: Clima lluvioso, colmena muy poblada..." class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl shadow-lg transition">
                            Continuar
                        </button>
                    </div>
                </form>
            </div>

            <!-- CAPA 2: CONFIRMACIÓN (Se superpone) -->
            <div x-show="showConfirm" style="display: none;" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-6">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900">¿Confirmar Registro?</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Estás a punto de registrar <strong><span x-text="form.cantidad_kg"></span> kg</strong>. <br>
                        <span class="text-red-500 font-semibold">Esta acción no se puede deshacer ni editar por motivos de trazabilidad.</span>
                    </p>
                </div>
                <div class="flex gap-3">
                    <button @click="showConfirm = false" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 rounded-xl transition">
                        Revisar
                    </button>
                    <button @click="executeSave()" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg transition">
                        Confirmar
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>