<div x-data="{
    show: false,
    view: 'list', // 'list', 'create', 'edit'
    isOnline: navigator.onLine,
    form: { id_local: '', titulo: '', contenido: '' },

    generateUUID() {
        if (window.crypto && window.crypto.randomUUID) return window.crypto.randomUUID();
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8); return v.toString(16);
        });
    },

    openCreate() {
        this.form = { id_local: '', titulo: '', contenido: '' };
        this.view = 'create';
    },

    openEdit(guia) {
        this.form = { id_local: guia.id_local, titulo: guia.titulo, contenido: guia.contenido };
        this.view = 'edit';
    },

    async save() {
        if (!this.form.titulo || !this.form.contenido) {
            this.$dispatch('notify', { message: 'Título y contenido obligatorios', type: 'error' });
            return;
        }
        
        const payload = { ...this.form, id_local: this.view === 'edit' ? this.form.id_local : this.generateUUID() };

        if (this.isOnline) {
            try {
                this.view === 'edit' ? await $wire.updateGuia(payload) : await $wire.storeGuia(payload);
                this.$dispatch('notify', { message: 'Guía guardada exitosamente', type: 'success' });
                this.view = 'list';
            } catch (e) {
                this.saveLocal(payload);
            }
        } else {
            this.saveLocal(payload);
        }
    },

    async saveLocal(payload) {
        try {
            await window.db.guias.put({ ...payload, synced: 0, created_at: new Date().toISOString() });
            this.$dispatch('notify', { message: 'Guardado offline localmente', type: 'info' });
            this.view = 'list';
        } catch (e) {}
    }
}"
@open-help-modal.window="show = true; view = 'list'"
@online.window="isOnline = true" 
@offline.window="isOnline = false"
class="relative z-[100]">

    <div x-show="show" x-transition.opacity style="display:none;" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

    <div x-show="show" x-transition.translate.y.50 style="display:none;" class="fixed inset-0 z-10 flex items-center justify-center p-4">
        
        <!-- Contenedor del Modal Responsivo -->
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col" @click.away="show = false">
            
            <!-- HEADER -->
            <div class="bg-yellow-400 px-6 py-4 flex justify-between items-center shrink-0">
                <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                    <span class="text-2xl">💡</span> Centro de Ayuda
                </h2>
                <button @click="show = false" class="text-gray-800 hover:text-black font-black text-2xl transition transform hover:scale-110">&times;</button>
            </div>

            <!-- CONTENIDO CON SCROLL -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
                
                <!-- VISTA 1: LISTADO DE GUÍAS -->
                <div x-show="view === 'list'">
                    @if($isAdmin)
                        <div class="flex justify-end mb-4">
                            <button @click="openCreate()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg shadow-md transition">
                                + Publicar Información
                            </button>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @forelse($guias as $guia)
                            <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-black text-lg text-gray-800">{{ $guia->titulo }}</h3>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-100 px-2 py-1 rounded-md">
                                        {{ \Carbon\Carbon::parse($guia->created_at)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $guia->contenido }}</p>
                                
                                @if($isAdmin)
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end gap-3">
                                        <button @click="openEdit({{ $guia->toJson() }})" class="text-xs font-bold text-blue-600 hover:text-blue-800">Editar</button>
                                        <button wire:click="deleteGuia({{ $guia->id }})" wire:confirm="¿Borrar esta guía?" class="text-xs font-bold text-red-500 hover:text-red-700">Eliminar</button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-500">
                                <span class="text-4xl mb-2 block">📚</span>
                                <p class="font-bold">Aún no hay información publicada.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $guias->links() }}
                    </div>
                </div>

                <!-- VISTA 2: FORMULARIO (SOLO ADMIN) -->
                <div x-show="view === 'create' || view === 'edit'" style="display:none;">
                    <button @click="view = 'list'" class="text-sm font-bold text-gray-500 hover:text-gray-800 mb-4 flex items-center gap-1">
                        &larr; Volver a las guías
                    </button>

                    <form @submit.prevent="save()" class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Título del Artículo</label>
                            <input type="text" x-model="form.titulo" required class="w-full border-gray-300 rounded-lg focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Contenido / Instrucciones</label>
                            <textarea x-model="form.contenido" rows="6" required class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition shadow-md">
                            Publicar Información
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>