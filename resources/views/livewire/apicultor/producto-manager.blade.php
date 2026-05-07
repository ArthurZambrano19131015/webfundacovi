<div x-data="{
    showModal: false,
    isEdit: false,
    isOnline: navigator.onLine,
    offlineItems: [],
    photoPreview: null,
    form: { id_local: '', id_apiario: '', nombre: '', precio: '', observaciones: '', foto_b64: null },

    generateUUID() {
        if (window.crypto && window.crypto.randomUUID) return window.crypto.randomUUID();
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    async loadOffline() {
        if (window.db && window.db.productos) {
            this.offlineItems = await window.db.productos.where('synced').equals(0).toArray();
        }
    },

    init() {
        setTimeout(() => { this.loadOffline(); }, 500);
    },

    openCreate() {
        this.isEdit = false;
        this.resetForm();
        this.showModal = true;
    },

    async toggleStatusLocal(item) {
        item.estado_activo = !item.estado_activo;

        if (this.isOnline && item.id) {
            await $wire.toggleStatus(item.id);
            this.$dispatch('notify', { message: 'Estado actualizado en servidor', type: 'success' });
        } else {
            try {
                if (!window.db) throw new Error('DB local inactiva');
                item.synced = 0;
                await window.db.productos.put(item);
                this.loadOffline();
                this.$dispatch('notify', { message: 'Estado actualizado (Offline)', type: 'info' });
            } catch (e) {
                this.$dispatch('notify', { message: 'Error: ' + e.message, type: 'error' });
            }
        }
    },

    openEdit(prod) {
        this.isEdit = true;
        this.photoPreview = prod.foto; // Ya está en Base64
        this.form = {
            id_local: prod.id_local,
            id_apiario: prod.id_apiario,
            nombre: prod.nombre,
            precio: prod.precio,
            observaciones: prod.observaciones || '',
            foto_b64: prod.foto
        };
        this.showModal = true;
    },

    // Convertir imagen a Base64 para soporte OFFLINE
    handleFile(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            this.photoPreview = e.target.result;
            this.form.foto_b64 = e.target.result; // Guardamos string base64
        };
        reader.readAsDataURL(file);
    },

    async save() {
        if (!this.form.nombre || !this.form.precio || !this.form.id_apiario) {
            this.$dispatch('notify', { message: 'Nombre, Precio y Apiario obligatorios', type: 'error' });
            return;
        }
        const payload = { ...this.form, id_local: this.isEdit ? this.form.id_local : this.generateUUID(), estado_activo: true };

        if (this.isOnline) {
            try {
                let response = this.isEdit ? await $wire.updateProducto(payload) : await $wire.storeProducto(payload);
                if (response && response.error) { this.$dispatch('notify', { message: response.error, type: 'error' }); return; }
                this.closeAll();
                this.$dispatch('notify', { message: 'Producto registrado', type: 'success' });
            } catch (e) { this.saveLocal(payload); }
        } else { this.saveLocal(payload); }
    },

    async saveLocal(payload) {
        try {
            if (!window.db) throw new Error('DB no disponible');
            const offlineData = { ...payload, synced: 0, created_at: new Date().toISOString() };
            await window.db.productos.put(offlineData);
            this.loadOffline();
            this.closeAll();
            this.$dispatch('notify', { message: 'Guardado en dispositivo (Offline)', type: 'info' });
        } catch (e) { this.$dispatch('notify', { message: 'Error local: ' + e.message, type: 'error' }); }
    },

    closeAll() { this.showModal = false;
        this.resetForm(); },
    resetForm() { this.photoPreview = null;
        this.form = { id_local: '', id_apiario: '', nombre: '', precio: '', observaciones: '', foto_b64: null }; }
}" @online.window="isOnline = true" @offline.window="isOnline = false">

    <!-- HEADER Y SÚPER FILTRO -->
    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-yellow-300 pb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Mis Productos</h2>
            <p class="text-sm font-bold text-yellow-600">Catálogo Comercial</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">

            <!-- USO DEL COMPONENTE REUTILIZABLE -->
            <x-filter-menu :hasActiveFilters="$hasFilters">
                <!-- Slots internos de filtrado -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Buscar Nombre</label>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Ej: Miel 500g..."
                        class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Apiario de Origen</label>
                    <select wire:model.live="filtro_apiario"
                        class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                        <option value="">Todos los apiarios</option>
                        @foreach ($apiarios as $api)
                            <option value="{{ $api->id }}">{{ $api->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Estado</label>
                    <select wire:model.live="filtro_estado"
                        class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                        <option value="">Todos los estados</option>
                        <option value="1">Solo Activos</option>
                        <option value="0">Solo Inactivos</option>
                    </select>
                </div>
            </x-filter-menu>

            <button @click="openCreate()"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition whitespace-nowrap">
                + Nuevo Producto
            </button>
        </div>
    </div>

    <!-- BANDEJA DE DATOS OFFLINE -->
    <template x-if="offlineItems.length > 0">
        <div class="mb-6 bg-yellow-50 border border-yellow-300 rounded-xl p-4 shadow-sm w-full col-span-full">
            <h3 class="font-bold text-yellow-800 flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Productos pendientes de Sincronización
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <template x-for="item in offlineItems" :key="item.id_local">
                    <div
                        class="bg-white p-3 rounded-lg shadow border-l-4 border-yellow-400 flex justify-between items-center opacity-80">
                        <div class="flex items-center gap-3">
                            <!-- Muestra la foto en miniatura si existe -->
                            <template x-if="item.foto_b64">
                                <img :src="item.foto_b64" class="w-10 h-10 rounded object-cover">
                            </template>
                            <div>
                                <p class="font-bold text-gray-800 text-sm" x-text="item.nombre"></p>
                                <p class="text-xs font-bold text-green-600" x-text="'$' + item.precio"></p>
                            </div>
                        </div>
                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded">Esperando
                            red...</span>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- GRID DE PRODUCTOS (Tipo Tienda) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($productos as $prod)
            <div
                class="bg-white rounded-2xl shadow-lg border {{ $prod->estado_activo ? 'border-yellow-200' : 'border-gray-300 opacity-75' }} overflow-hidden flex flex-col hover:shadow-xl transition">
                <!-- Imagen Base64 -->
                <div class="h-48 w-full bg-gray-100 flex items-center justify-center relative">
                    @if ($prod->foto)
                        <img src="{{ $prod->foto }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl text-gray-300">🍯</span>
                    @endif
                    <div
                        class="absolute top-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded-md font-bold backdrop-blur-sm">
                        ${{ number_format($prod->precio, 0) }}
                    </div>
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-black text-lg text-gray-800">{{ $prod->nombre }}</h3>
                    <p class="text-xs font-bold text-blue-600 mt-1">📍 {{ $prod->apiario->nombre }}</p>
                    <p class="text-xs text-gray-500 mt-2 line-clamp-2 flex-1">{{ $prod->observaciones }}</p>

                    <div class="grid grid-cols-2 gap-2 border-t border-gray-100 pt-3 mt-4">
                        <button @click="openEdit({{ $prod->toJson() }})"
                            class="py-1.5 text-xs font-bold rounded border border-blue-200 text-blue-600 hover:bg-blue-50">✏️
                            Editar</button>
                        <button @click="toggleStatusLocal({{ $prod->toJson() }})"
                            class="py-1.5 text-xs font-bold rounded border {{ $prod->estado_activo ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                            {{ $prod->estado_activo ? 'Ocultar' : 'Activar' }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                <span class="text-4xl">🛍️</span>
                <p class="mt-2 text-gray-500 font-bold">No hay productos que coincidan con los filtros.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $productos->links() }}</div>

    <!-- MODAL -->
    <div wire:ignore.self x-show="showModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm"
        style="display:none;" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative" @click.away="closeAll()">
            <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Editar Producto' : 'Nuevo Producto'"></h3>
                <button @click="closeAll()"
                    class="text-gray-600 hover:text-gray-900 font-bold text-2xl">&times;</button>
            </div>

            <form @submit.prevent="save()" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <!-- Foto a Base64 -->
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="w-32 h-32 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 overflow-hidden flex items-center justify-center relative">
                        <template x-if="photoPreview"><img :src="photoPreview"
                                class="w-full h-full object-cover"></template>
                        <template x-if="!photoPreview"><span
                                class="text-gray-400 text-sm font-bold text-center">Subir<br>Foto</span></template>
                        <input type="file" accept="image/*" @change="handleFile($event)"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre del Producto</label>
                    <input type="text" x-model="form.nombre" required
                        class="mt-1 w-full border-gray-300 rounded-md focus:ring-yellow-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio (COP)</label>
                        <input type="number" x-model="form.precio" required
                            class="mt-1 w-full border-gray-300 rounded-md focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apiario de Origen</label>
                        <select x-model="form.id_apiario" required
                            class="mt-1 w-full border-gray-300 rounded-md focus:ring-yellow-500">
                            <option value="">Seleccione...</option>
                            @foreach ($apiarios as $api)
                                <option value="{{ $api->id }}">{{ $api->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Observaciones / Descripción</label>
                    <textarea x-model="form.observaciones" rows="2"
                        class="mt-1 w-full border-gray-300 rounded-md focus:ring-yellow-500"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition">Guardar</button>
            </form>
        </div>
    </div>
</div>
