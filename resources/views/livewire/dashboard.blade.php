<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<!-- Ocupa el 100% de la altura restante del <main> -->
<div x-data="{ isOnline: navigator.onLine }" @online.window="isOnline = true" @offline.window="isOnline = false"
    class="flex flex-col w-full h-full">

    <!-- 1. HERO BANNER (Full Width Real) -->
    <div class="shrink-0 w-full h-40 md:h-56 bg-cover bg-center flex items-center shadow-md relative"
        style="background-image: url('{{ asset('img/hero-bg.png') }}'); background-color: #d97706; background-blend-mode: multiply;">
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
        <div class="relative z-10 px-6 md:px-12">
            <h1 class="text-2xl md:text-4xl font-black text-white drop-shadow-md leading-tight">
                Optimiza la Producción y Calidad 🐝🍯
            </h1>
            <p class="hidden md:block text-base text-yellow-50 font-medium mt-1 drop-shadow-md max-w-xl">
                Sistema de Control y Trazabilidad Apícola - FUNDACOVI.
            </p>
        </div>
    </div>

    <!-- 2. GRID INFERIOR CON PADDING PROPIO (Ocupa el resto del espacio en PC) -->
    <div class="flex-1 p-4 md:p-6 grid grid-cols-1 lg:grid-cols-3 gap-6 lg:overflow-hidden">

        <!-- COLUMNA A: PERFIL DEL USUARIO -->
        <div
            class="bg-[#E9EFA7] rounded-3xl shadow-lg border border-yellow-300 flex flex-col h-full overflow-hidden relative">

            <!-- Avatar & Nombre -->
            <div class="px-5 flex items-end gap-3 -mt-10 mb-3 shrink-0 relative z-10">
                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('img/default-avatar.png') }}"
                    alt="Foto"
                    class="w-20 h-20 rounded-2xl  border-4 border-[#E9EFA7] shadow-sm object-cover bg-white">
                <div class="pb-1 overflow-hidden">
                    <h2 class="text-xl font-black text-gray-900 leading-none truncate">{{ $user->nombre_completo }}</h2>
                    <span
                        class="inline-block bg-yellow-400 text-yellow-900 text-[10px] px-2 rounded font-bold mt-1 uppercase tracking-wider">
                        {{ $user->role->nombre_rol }}
                    </span>
                </div>
            </div>

            <!-- Datos (Con Estados Vacíos / Zero-States) -->
            <div class="px-4 pb-4 space-y-2 flex-1 flex flex-col justify-center">

                <!-- Apiarios -->
                <div class="bg-white/60 rounded-xl p-3 shadow-sm border border-white/50">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-base">🐝</span>
                        <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wide">Tus Apiarios</h3>
                    </div>
                    @if ($apiariosCount > 0)
                        <p class="text-xs font-bold text-gray-800 ml-6">{{ $apiariosCount }} Registrados en el sistema.
                        </p>
                    @else
                        <p class="text-[11px] text-red-600 font-bold ml-6">No tienes apiarios. <a
                                href="{{ route('admin.apiarios') }}" class="underline">¡Registra el primero!</a></p>
                    @endif
                </div>

                <!-- Ubicación -->
                <div class="bg-white/60 rounded-xl p-3 shadow-sm border border-white/50">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-base">📍</span>
                        <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wide">Ubicación</h3>
                    </div>
                    @if ($apiariosCount > 0)
                        <p class="text-[11px] text-gray-700 ml-6 leading-tight">Presencia en: <span
                                class="font-bold text-gray-900">{{ $municipios }}</span>.</p>
                    @else
                        <p class="text-[11px] text-gray-500 ml-6 leading-tight">Registra apiarios para definir tu zona.
                        </p>
                    @endif
                </div>

                <!-- Producción -->
                <div class="bg-white/60 rounded-xl p-3 shadow-sm border border-white/50">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-base">🍯</span>
                        <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wide">Tu Producción</h3>
                    </div>
                    @if ($miProduccion > 0)
                        <div class="flex items-baseline gap-1 mt-0.5 ml-6">
                            <span
                                class="text-xl font-black text-yellow-600 leading-none">{{ number_format($miProduccion, 1) }}</span>
                            <span class="text-xs font-bold text-gray-600">kg recolectados</span>
                        </div>
                        <p class="text-[10px] font-bold text-gray-500 mt-1 ml-6 uppercase">Aportas el <span
                                class="text-yellow-600">{{ $porcentajeAporte }}%</span> del total de FUNDACOVI.</p>
                    @else
                        <p class="text-[11px] text-yellow-600 font-bold ml-6">0.0 kg. <a
                                href="{{ route('apicultor.cosechas') }}" class="underline">¡Registra tu primera
                                cosecha!</a></p>
                    @endif
                </div>

            </div>
        </div>

        <!-- COLUMNA B: MAPA DE GOOGLE -->
        <div
            class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden flex flex-col relative h-64 lg:h-full">
            <div class="absolute top-0 w-full flex justify-center z-20">
                <div
                    class="bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest px-6 py-1 rounded-b-lg shadow-md">
                    Ubicación Regional
                </div>
            </div>

            <!-- Lógica Offline/Online de Alpine -->
            <template x-if="isOnline">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1014083.5683701633!2d-73.66225675200234!3d8.215569421877626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e6479b6348ebddb%3A0x19a0a1a5b06d4e!2sNorte%20de%20Santander%2C%20Colombia!5e0!3m2!1sen!2s!4v1700000000000!5m2!1sen!2s"
                    class="w-full h-full border-0" allowfullscreen="" loading="lazy"></iframe>
            </template>

            <template x-if="!isOnline">
                <div class="w-full h-full bg-gray-100 flex flex-col items-center justify-center p-6 text-center">
                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <h3 class="font-black text-gray-700 text-sm">Sin conexión a Internet</h3>
                    <p class="text-[10px] text-gray-500 mt-1">El mapa interactivo requiere conexión.</p>
                </div>
            </template>
        </div>

        <!-- COLUMNA C: CARRUSEL DE PRODUCTOS -->
        <div x-data="{
            init() {
                const el = this.$refs.slider;
                setInterval(() => {
                    if (el.scrollLeft + el.clientWidth >= el.scrollWidth - 5) el.scrollTo({ left: 0, behavior: 'smooth' });
                    else el.scrollBy({ left: 120, behavior: 'smooth' });
                }, 2500);
            }
        }"
            class="bg-[#8A9A5B] rounded-3xl shadow-lg border border-yellow-200 flex flex-col h-64 lg:h-full relative">

            <div class="absolute top-0 w-full flex justify-center z-10">
                <div
                    class="bg-yellow-500 text-white font-black text-[10px] uppercase tracking-widest px-6 py-1 rounded-b-lg shadow-md">
                    Catálogo Comercial
                </div>
            </div>

            <div class="flex-1 flex flex-col justify-center w-full">
                <div x-ref="slider"
                    class="flex overflow-x-auto gap-4 px-6 snap-x snap-mandatory scrollbar-hide w-full items-center">
                    <!-- Productos estáticos .png -->
                    <div
                        class="shrink-0 snap-center w-24 h-24 rounded-full border-4 border-white/80 shadow-lg overflow-hidden bg-[#F3EAC0]">
                        <img src="{{ asset('img/prod1.png') }}" class="w-full h-full object-cover">
                    </div>
                    <div
                        class="shrink-0 snap-center w-24 h-24 rounded-full border-4 border-white/80 shadow-lg overflow-hidden bg-[#F3EAC0]">
                        <img src="{{ asset('img/prod2.png') }}" class="w-full h-full object-cover">
                    </div>
                    <div
                        class="shrink-0 snap-center w-24 h-24 rounded-full border-4 border-white/80 shadow-lg overflow-hidden bg-[#F3EAC0]">
                        <img src="{{ asset('img/prod3.png') }}" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <div class="p-4 w-full shrink-0">
                <p
                    class="text-white text-[10px] font-bold bg-black/30 text-center py-2 rounded-xl shadow-inner uppercase tracking-wider">
                    Módulo habilitado en el Sprint 4
                </p>
            </div>
        </div>

    </div>
</div>
