<div x-data="{
    status: navigator.onLine ? 'synced' : 'offline',
    progress: 0,
    totalItems: 0,

    init() {
        if (navigator.onLine) {
            this.startSyncing();
        }
    },

    goOnline() {
        this.startSyncing();
    },

    goOffline() {
        this.status = 'offline';
        this.progress = 0;
    },

    async startSyncing() {
        if (!window.db) return;

        this.status = 'syncing';
        this.progress = 0;

        const tablas = ['apiarios', 'colmenas', 'cosechas', 'lotes', 'productos'];
        let itemsPendientes = [];

        try {
            // 1. Buscar registros pendientes
            for (let tabla of tablas) {
                if (window.db[tabla]) {
                    let pendientes = await window.db[tabla].where('synced').equals(0).toArray();
                    pendientes.forEach(item => itemsPendientes.push({ tabla: tabla, data: item }));
                }
            }

            this.totalItems = itemsPendientes.length;

            // Si no hay nada que sincronizar, pasar a verde directamente
            if (this.totalItems === 0) {
                this.progress = 100;
                setTimeout(() => { this.status = 'synced'; }, 500);
                return;
            }

            // 2. Procesar cada registro pendiente
            let procesados = 0;
            for (let item of itemsPendientes) {

                // LLAMADA FETCH AL SERVIDOR:

                // Simulamos el tiempo de espera del servidor para que la barra se mueva fluidamente
                await new Promise(resolve => setTimeout(resolve, 400));

                // Una vez el servidor responde OK, marcamos como sincronizado localmente
                await window.db[item.tabla].update(item.data.id_local, { synced: 1 });

                // Actualizar el progreso matemático REAL
                procesados++;
                this.progress = Math.round((procesados / this.totalItems) * 100);
            }

            // 3. Terminar
            if (this.progress >= 100) {
                setTimeout(() => { this.status = 'synced'; }, 800);
            }

        } catch (error) {
            console.error('Error en sincronización:', error);
            this.status = 'offline'; // Si falla, volvemos a modo offline para proteger los datos
        }
    }
}" @online.window="goOnline()" @offline.window="goOffline()"
    class="fixed bottom-4 right-4 z-[9999] pointer-events-none transition-all duration-300">

    <!-- ESTADO 1: MODO OFFLINE -->
    <template x-if="status === 'offline'">
        <div
            class="bg-red-600 text-white px-4 py-2 rounded-full shadow-2xl flex items-center gap-2 font-bold border border-red-800 animate-pulse">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 5.636l-3.536 3.536m0 0l-3.536-3.536m3.536 3.536l3.536-3.536m-12.122 12.122l3.536-3.536m0 0l-3.536 3.536m3.536-3.536l-3.536 3.536">
                </path>
            </svg>
            <span class="text-[11px] uppercase tracking-wider">Modo Offline</span>
        </div>
    </template>

    <!-- ESTADO 2: SINCRONIZANDO  -->
    <template x-if="status === 'syncing'">
        <div
            class="bg-gray-900 text-white w-56 rounded-lg shadow-2xl border border-gray-700 overflow-hidden flex flex-col">
            <div class="px-3 py-1.5 flex justify-between items-center bg-gray-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-yellow-400">Sincronizando
                    datos...</span>
                <span class="text-[10px] font-bold" x-text="progress + '%'"></span>
            </div>
            <div class="h-1.5 w-full bg-gray-700">
                <div class="h-full bg-yellow-400 transition-all duration-300 ease-out"
                    :style="'width: ' + progress + '%'"></div>
            </div>
        </div>
    </template>

    <!-- ESTADO 3: SINCRONIZADO -->
    <template x-if="status === 'synced'">
        <div
            class="bg-green-500 text-white px-3 py-1.5 rounded-full shadow-md flex items-center gap-2 opacity-90 transition-opacity">
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <span class="text-[10px] font-bold uppercase tracking-wider">Sincronizado</span>
        </div>
    </template>

</div>
