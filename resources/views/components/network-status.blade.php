<div x-data="{ online: navigator.onLine }" 
    @online.window="online = true" 
    @offline.window="online = false"
    class="fixed bottom-4 right-4 z-[9999] pointer-events-none">
    
    <!-- Alerta de Modo Offline -->
    <template x-if="!online">
        <div class="bg-red-600 text-white px-4 py-2 rounded-full shadow-2xl flex items-center gap-2 font-bold animate-bounce border-2 border-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 0l-3.536-3.536m3.536 3.536l3.536-3.536m-12.122 12.122l3.536-3.536m0 0l-3.536 3.536m3.536-3.536l-3.536 3.536"></path>
            </svg>
            <span class="text-sm uppercase tracking-wider">Modo Offline: Guardado Local</span>
        </div>
    </template>

    <!-- Indicador de Conexión (Sutil) -->
    <template x-if="online">
        <div class="bg-green-500 text-white px-3 py-1 rounded-full shadow-md flex items-center gap-2 opacity-80">
            <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
            <span class="text-xs font-medium uppercase">Sincronizado</span>
        </div>
    </template>
</div>