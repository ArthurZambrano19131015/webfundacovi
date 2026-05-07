<div x-data="{
    showModal: false,
    showConfirm: false,
    isSubmitting: false,
    step: 1,
    isOnline: navigator.onLine,
    offlineItems:[],

    cosechasDisp: @js($cosechasDisponibles),
    estandares: @js($estandares),

    form: {
        id_local: '',
        cosechas_ids: [],
        resultados: {},
        estado_aprobacion: 'PENDIENTE'
    },

    async loadOffline() {
        if (window.db && window.db.lotes) {
            this.offlineItems = await window.db.lotes.where('synced').equals(0).toArray();
        }
    },

    init() {
        this.estandares.forEach(est => {
            this.form.resultados[est.id] = { valor: '', cumple: null };
        });
        setTimeout(() => { this.loadOffline(); }, 500); 
    },

    generateUUID() {
        if (window.crypto && window.crypto.randomUUID) return window.crypto.randomUUID();
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    openWizard() {
        if (this.cosechasDisp.length === 0) {
            this.$dispatch('notify', { message: 'No hay cosechas disponibles para agrupar', type: 'warning' });
            return;
        }
        if (this.estandares.length === 0) {
            this.$dispatch('notify', { message: 'El administrador no ha configurado estándares', type: 'error' });
            return;
        }
        this.form.cosechas_ids = [];
        this.init();
        this.step = 1;
        this.showModal = true;
        this.showConfirm = false;
        this.isSubmitting = false;
    },

    toggleCosecha(id_local) {
        let index = this.form.cosechas_ids.indexOf(id_local);
        if (index > -1) {
            this.form.cosechas_ids.splice(index, 1);
        } else {
            this.form.cosechas_ids.push(id_local);
        }
    },

    requestSave() {
        let anyEmpty = this.estandares.some(est => this.form.resultados[est.id].valor === '');
        if (anyEmpty) {
            this.$dispatch('notify', { message: 'Debes completar los resultados de todos los parámetros', type: 'warning' });
            return;
        }
        this.evaluarSilenciosamente();

        this.showConfirm = true;
    },

    evaluarSilenciosamente() {
        let allPassed = true;
        this.estandares.forEach(est => {
            let res = this.form.resultados[est.id];
            let val = parseFloat(res.valor);

            let cumple = true;
            if (est.valor_minimo !== null && val < parseFloat(est.valor_minimo)) cumple = false;
            if (est.valor_maximo !== null && val > parseFloat(est.valor_maximo)) cumple = false;

            res.cumple = cumple;
            if (!cumple) allPassed = false;
        });

        this.form.estado_aprobacion = allPassed ? 'APROBADO' : 'RECHAZADO';
    },

    async executeSave() {
        if (this.isSubmitting) return;
        this.isSubmitting = true;

        const payload = { ...this.form, id_local: this.generateUUID() };

        if (this.isOnline) {
            try {
                let response = await $wire.storeLote(payload);
                if (response && response.error) {
                    this.$dispatch('notify', { message: response.error, type: 'error' });
                    this.isSubmitting = false;
                    return;
                }
                this.$dispatch('notify', { message: 'Lote registrado con éxito', type: 'success' });
                this.closeAll();
                setTimeout(() => location.reload(), 1500);
            } catch (e) {
                this.saveLocal(payload);
            }
        } else {
            this.saveLocal(payload);
        }
    },

    async saveLocal(payload) {
        try {
            if (!window.db) throw new Error('DB no disponible');
            const offlineData = { ...payload, synced: 0, codigo_lote: 'PENDIENTE-SYNC', created_at: new Date().toISOString() };
            await window.db.lotes.put(offlineData);

            this.closeAll();
            this.$dispatch('notify', { message: 'Lote guardado en dispositivo (Offline)', type: 'info' });
            setTimeout(() => location.reload(), 1500);
        } catch (e) {
            this.$dispatch('notify', { message: 'Error local: ' + e.message, type: 'error' });
            this.isSubmitting = false;
        }
    },

    closeAll() {
        this.showModal = false;
        this.showConfirm = false;
    }
}" @online.window="isOnline = true" @offline.window="isOnline = false">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Control de Calidad</h2>
            <p class="text-sm font-bold text-yellow-600">Evaluación de Lotes</p>
        </div>

        <button @click="openWizard()"
            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition whitespace-nowrap">
            + Registrar Análisis de Laboratorio
        </button>
    </div>

    <!-- BANDEJA DE DATOS OFFLINE -->
    <template x-if="offlineItems.length > 0">
        <div class="mb-6 bg-yellow-50 border border-yellow-300 rounded-xl p-4 shadow-sm">
            <h3 class="font-bold text-yellow-800 flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Lotes pendientes de Sincronización
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <template x-for="item in offlineItems" :key="item.id_local">
                    <div class="bg-white p-3 rounded-lg shadow border-l-4 border-yellow-400 flex justify-between items-center opacity-80">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">LOTE OFFLINE</p>
                            <p class="text-xs text-gray-500">Evaluación pendiente de envío</p>
                        </div>
                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded">Esperando red...</span>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- VISTA MÓVIL -->
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($lotes as $lote)
            <div
                class="bg-white rounded-xl shadow-md p-5 border-l-4 {{ $lote->estado_aprobacion == 'APROBADO' ? 'border-green-500' : ($lote->estado_aprobacion == 'RECHAZADO' ? 'border-red-500' : 'border-yellow-400') }}">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-black text-xl text-gray-800">{{ $lote->codigo_lote }}</h3>
                    <span
                        class="px-2 py-1 text-xs font-bold rounded-full {{ $lote->estado_aprobacion == 'APROBADO' ? 'bg-green-100 text-green-800' : ($lote->estado_aprobacion == 'RECHAZADO' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ $lote->estado_aprobacion }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mb-3">{{ $lote->cosechas->count() }} cosechas agrupadas</p>

                <div class="bg-gray-50 p-3 rounded-lg text-sm">
                    @foreach ($lote->resultados as $res)
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">{{ $res->estandar->parametro }}:</span>
                            <span
                                class="font-bold {{ $res->cumple_estandar ? 'text-green-600' : 'text-red-600' }}">{{ $res->valor_obtenido }}
                                {{ $res->estandar->unidad_medida }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 p-6 bg-white rounded-xl shadow">No hay lotes evaluados.</div>
        @endforelse
    </div>

    <!-- VISTA DESKTOP: Tabla -->
    <div class="hidden md:block bg-white shadow-xl rounded-xl overflow-hidden border border-yellow-200">
        <table class="min-w-full">
            <thead class="bg-yellow-400 text-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left font-bold text-xs uppercase">Código de Lote</th>
                    <th class="px-6 py-3 text-left font-bold text-xs uppercase">Cosechas</th>
                    <th class="px-6 py-3 text-left font-bold text-xs uppercase">Resultados Lab</th>
                    <th class="px-6 py-3 text-center font-bold text-xs uppercase">Dictamen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lotes as $lote)
                    <tr class="hover:bg-yellow-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-700">{{ $lote->codigo_lote }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $lote->cosechas->count() }} Cosechas</td>
                        <td class="px-6 py-4 text-sm">
                            @foreach ($lote->resultados as $res)
                                <span
                                    class="inline-block mr-2 {{ $res->cumple_estandar ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $res->estandar->parametro }}: <strong>{{ $res->valor_obtenido }}</strong>
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-3 py-1 text-xs font-bold rounded-full {{ $lote->estado_aprobacion == 'APROBADO' ? 'bg-green-100 text-green-800' : ($lote->estado_aprobacion == 'RECHAZADO' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $lote->estado_aprobacion }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No hay lotes evaluados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-6">{{ $lotes->links() }}</div>

    <!-- WIZARD MODAL -->
    <div wire:ignore.self x-show="showModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
        style="display: none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden relative"
            @click.away="showConfirm ? null : closeAll()">

            <!-- CONTENIDO PRINCIPAL DEL MODAL -->
            <div x-show="!showConfirm" x-transition>
                <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">
                        <span x-show="step === 1">Paso 1: Agrupar Cosechas</span>
                        <span x-show="step === 2">Paso 2: Registro de Resultados</span>
                    </h3>
                    <button @click="closeAll()"
                        class="text-gray-600 hover:text-gray-900 font-bold text-2xl">&times;</button>
                </div>

                <div class="p-6">
                    <!-- PASO 1: SELECCIONAR COSECHAS -->
                    <div x-show="step === 1" x-transition>
                        <p class="text-sm text-gray-600 mb-4">Selecciona las cosechas que formarán parte de este lote de
                            calidad:</p>

                        <div class="max-h-64 overflow-y-auto space-y-2 mb-6">
                            <template x-for="cos in cosechasDisp" :key="cos.id_local">
                                <label
                                    class="flex items-center p-3 border rounded-lg hover:bg-yellow-50 cursor-pointer transition"
                                    :class="{ 'border-yellow-500 bg-yellow-50': form.cosechas_ids.includes(cos.id_local) }">
                                    <input type="checkbox" :value="cos.id_local" @click="toggleCosecha(cos.id_local)"
                                        class="rounded text-yellow-500 focus:ring-yellow-500 mr-3 h-5 w-5">
                                    <div>
                                        <p class="font-bold text-gray-800" x-text="cos.cantidad_kg + ' kg'"></p>
                                        <p class="text-xs text-gray-500"
                                            x-text="'Extraído el ' + cos.fecha_recoleccion + ' | ' + cos.colmena.apiario.nombre">
                                        </p>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <div class="flex justify-end">
                            <button
                                @click="if(form.cosechas_ids.length > 0) step = 2; else $dispatch('notify', {message: 'Selecciona al menos 1 cosecha', type:'error'})"
                                class="bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg">
                                Siguiente &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- PASO 2: FORMULARIO DINÁMICO CIEGO -->
                    <div x-show="step === 2" style="display:none;" x-transition>
                        <p class="text-sm text-gray-600 mb-4 text-center">Introduce los valores arrojados por el
                            laboratorio. El sistema determinará la aprobación final basándose en la normativa vigente.
                        </p>

                        <div class="space-y-4 mb-6 max-h-64 overflow-y-auto px-4">
                            <template x-for="est in estandares" :key="est.id">
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-200">

                                    <div class="mb-2 sm:mb-0">
                                        <p class="font-bold text-gray-800" x-text="est.parametro"></p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="number" step="0.01" x-model="form.resultados[est.id].valor"
                                            required
                                            class="w-32 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-center font-bold"
                                            placeholder="0.00">
                                        <span class="text-sm font-bold text-gray-500 w-10"
                                            x-text="est.unidad_medida"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between border-t pt-4">
                            <button @click="step = 1" class="text-gray-600 font-bold px-4 hover:text-gray-900">&larr;
                                Volver</button>
                            <button @click="requestSave()"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-lg shadow-md transition">
                                Guardar Resultados
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAPA 3: CONFIRMACIÓN DE GUARDADO -->
            <div x-show="showConfirm" style="display: none;" x-transition class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">¿Confirmar Registro?</h3>
                <p class="text-gray-600 mb-6">
                    Al confirmar, el sistema analizará los resultados ingresados y emitirá un dictamen de calidad que
                    <strong>no podrá ser alterado</strong>.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button @click="showConfirm = false" :disabled="isSubmitting"
                        class="w-full sm:w-auto px-6 py-3 bg-gray-200 text-gray-800 font-bold rounded-xl hover:bg-gray-300 transition">
                        Revisar de nuevo
                    </button>
                    <!-- BOTÓN BLOQUEABLE -->
                    <button @click="executeSave()" :disabled="isSubmitting"
                        class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="isSubmitting ? 'Registrando...' : 'Confirmar y Evaluar'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
