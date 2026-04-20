<div x-data="{
    notices: [],
    add(notice) {
        notice.id = Date.now();
        this.notices.push(notice);
        // Auto eliminar después de 4 segundos
        setTimeout(() => { this.remove(notice.id); }, 4000);
    },
    remove(id) {
        this.notices = this.notices.filter(n => n.id !== id);
    }
}" @notify.window="add($event.detail)"
    class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none">

    <template x-for="notice in notices" :key="notice.id">
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
            class="flex items-center p-4 w-full max-w-sm text-gray-800 bg-white rounded-xl shadow-2xl border-l-4 pointer-events-auto"
            :class="{
                'border-green-500': notice.type === 'success',
                'border-red-500': notice.type === 'error',
                'border-yellow-500': notice.type === 'warning',
                'border-blue-500': notice.type === 'info'
            }">

            <!-- Iconos Dinámicos -->
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg"
                :class="{
                    'text-green-500 bg-green-100': notice.type === 'success',
                    'text-red-500 bg-red-100': notice.type === 'error',
                    'text-yellow-600 bg-yellow-100': notice.type === 'warning',
                    'text-blue-500 bg-blue-100': notice.type === 'info'
                }">
                <template x-if="notice.type === 'success'">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </template>
                <template x-if="notice.type === 'error'">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </template>
                <template x-if="notice.type === 'warning'">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                </template>
            </div>

            <!-- Mensaje -->
            <div class="ml-3 text-sm font-medium" x-text="notice.message"></div>

            <!-- Botón Cerrar Manual -->
            <button @click="remove(notice.id)"
                class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 h-8 w-8">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
            </button>
        </div>
    </template>
</div>
