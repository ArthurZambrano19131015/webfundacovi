<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<!-- Importamos Leaflet (Mapas Gratuitos) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div x-data="{ isOnline: navigator.onLine }" @online.window="isOnline = true" @offline.window="isOnline = false"
    class="flex flex-col w-full h-full pb-6">

    <!-- 1. HERO BANNER -->
    <div class="shrink-0 w-full h-40 md:h-56 bg-cover bg-center flex items-center shadow-md relative rounded-b-3xl md:rounded-3xl border border-yellow-600/30"
        style="background-image: url('{{ asset('img/hero-bg.png') }}'); background-color: #d97706; background-blend-mode: multiply;">
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent rounded-b-3xl md:rounded-3xl"></div>
        <div class="relative z-10 px-6 md:px-12">
            <h1 class="text-2xl md:text-4xl font-black text-white drop-shadow-md leading-tight">
                Optimiza la Producción y Calidad 🐝🍯
            </h1>
            <p class="hidden md:block text-base text-yellow-50 font-medium mt-1 drop-shadow-md max-w-xl">
                Sistema de Control y Trazabilidad Apícola - FUNDACOVI.
            </p>
        </div>
    </div>

    <!-- 2. GRID INFERIOR  -->
    <div class="flex-1 mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 lg:overflow-hidden px-4 md:px-0">

        <!-- COLUMNA A: PERFIL DEL USUARIO -->
        <div class="bg-[#E9EFA7] rounded-3xl shadow-lg border border-yellow-300 flex flex-col h-full overflow-hidden relative">
            <div class="h-24 w-full bg-cover bg-center shrink-0 opacity-80" style="background-image: url('{{ asset('img/apicultor-bg.png') }}');"></div>
            
            <div class="px-5 flex items-end gap-3 -mt-10 mb-3 shrink-0 relative z-10">
                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('img/default-avatar.png') }}" alt="Foto"
                    class="w-20 h-20 rounded-2xl border-4 border-[#E9EFA7] shadow-sm object-cover bg-white">
                <div class="pb-1 overflow-hidden">
                    <h2 class="text-xl font-black text-gray-900 leading-none truncate">{{ $user->nombre_completo }}</h2>
                    <span class="inline-block bg-yellow-400 text-yellow-900 text-[10px] px-2 rounded font-bold mt-1 uppercase tracking-wider">
                        {{ $user->role->nombre_rol }}
                    </span>
                </div>
            </div>

            <div class="px-4 pb-4 flex-1 grid grid-cols-2 gap-3 content-center">
                <div class="bg-white/60 rounded-xl p-3 shadow-sm border border-white/50 h-full flex flex-col">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-base">🐝</span>
                        <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wide">Tus Apiarios</h3>
                    </div>
                    @if ($apiariosCount > 0)
                        <p class="text-xs font-bold text-gray-800 ml-6">{{ $apiariosCount }} Registrados.</p>
                    @else
                        <p class="text-[11px] text-red-600 font-bold ml-6">Sin apiarios. <a href="{{ route('admin.apiarios') }}" class="underline">¡Registra uno!</a></p>
                    @endif
                </div>

                <div class="bg-white/60 rounded-xl p-3 shadow-sm border border-white/50 h-full flex flex-col">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-base">📍</span>
                        <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wide">Ubicación</h3>
                    </div>
                    @if ($apiariosCount > 0)
                        <p class="text-[11px] text-gray-700 ml-6 leading-tight">Zonas: <span class="font-bold text-gray-900">{{ $municipios }}</span></p>
                    @else
                        <p class="text-[11px] text-gray-500 ml-6 leading-tight">Sin ubicación definida.</p>
                    @endif
                </div>

                <div class="bg-white/60 rounded-xl p-3 shadow-sm border border-white/50 h-full flex flex-col col-span-2">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-base">🍯</span>
                        <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wide">Tu Producción</h3>
                    </div>
                    @if ($miProduccion > 0)
                        <div class="flex items-baseline gap-1 mt-0.5 ml-6">
                            <span class="text-xl font-black text-yellow-600 leading-none">{{ number_format($miProduccion, 1) }}</span>
                            <span class="text-xs font-bold text-gray-600">kg recolectados</span>
                        </div>
                        <p class="text-[10px] font-bold text-gray-500 mt-1 ml-6 uppercase">Aportas el <span class="text-yellow-600">{{ $porcentajeAporte }}%</span> del total.</p>
                    @else
                        <p class="text-[11px] text-yellow-600 font-bold ml-6">0.0 kg. <a href="{{ route('apicultor.cosechas') }}" class="underline">¡Registra cosecha!</a></p>
                    @endif
                </div>
            </div>
        </div>

        <!-- COLUMNA B: MAPA INTERACTIVO -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden flex flex-col relative h-64 lg:h-full z-10"
             x-data="{
                initMap() {
                    // Si no hay red, no intentamos cargar el mapa
                    if(!this.isOnline) return;

                    setTimeout(() => {
                        // Coordenadas centrales de Norte de Santander
                        var map = L.map('mapa-apiarios').setView([8.0, -72.7], 8);
                        
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(map);

                        const puntos = @js($apiariosMap);
                        
                        // Generar un marcador por cada apiario
                        puntos.forEach(p => {
                            L.marker([p.lat, p.lng])
                             .addTo(map)
                             .bindPopup('<b>' + p.nombre + '</b><br><span class=\'text-xs text-gray-500\'>' + p.productor + '</span>');
                        });
                    }, 200);
                }
             }" x-init="initMap()">
            
            <div class="absolute top-0 w-full flex justify-center z-20 pointer-events-none">
                <div class="bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest px-6 py-1 rounded-b-lg shadow-md">
                    Ubicación Regional
                </div>
            </div>

            <!-- Mapa (Solo si hay conexión) -->
            <template x-if="isOnline">
                <div id="mapa-apiarios" class="w-full h-full z-10"></div>
            </template>

            <!-- Fallback Offline -->
            <template x-if="!isOnline">
                <div class="w-full h-full bg-gray-100 flex flex-col items-center justify-center p-6 text-center z-10">
                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                if (!el) return;
                setInterval(() => {
                    if (el.scrollLeft + el.clientWidth >= el.scrollWidth - 5) el.scrollTo({ left: 0, behavior: 'smooth' });
                    else el.scrollBy({ left: 140, behavior: 'smooth' });
                }, 3000);
            }
        }"
            class="bg-lime-800 rounded-3xl shadow-lg border border-lime-900 flex flex-col h-64 lg:h-full relative overflow-hidden">

            <div class="absolute top-0 w-full flex justify-center z-10">
                <div class="bg-yellow-500 text-white font-black text-[10px] uppercase tracking-widest px-6 py-1 rounded-b-lg shadow-md">
                    Catálogo Comercial
                </div>
            </div>

            <div class="flex-1 flex flex-col justify-center w-full">
                @if ($productos->count() > 0)
                    <div x-ref="slider" class="flex overflow-x-auto gap-4 px-6 snap-x snap-mandatory scrollbar-hide w-full items-center">
                        @foreach ($productos as $prod)
                            <!-- Tarjeta de Producto Cuadrada con Puntas Redondas -->
                            <div class="shrink-0 snap-center w-28 bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col group cursor-pointer border border-lime-700">
                                
                                <!-- Foto (Mitad superior) -->
                                <div class="h-24 w-full bg-lime-50 relative overflow-hidden flex items-center justify-center">
                                    @if ($prod->foto)
                                        <img src="{{ $prod->foto }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-4xl">🍯</span>
                                    @endif
                                    
                                    <!-- Hover Oscuro -->
                                    <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <span class="text-white text-sm font-black">${{ number_format($prod->precio, 0) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Nombre (Mitad inferior) -->
                                <div class="p-2 flex-1 flex items-center justify-center text-center">
                                    <h3 class="text-[11px] font-bold text-gray-800 leading-tight line-clamp-2">{{ $prod->nombre }}</h3>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center text-white/80 p-6 text-center">
                        <span class="text-4xl mb-2">🛍️</span>
                        <p class="text-xs font-bold uppercase tracking-wider">Aún no hay productos</p>
                    </div>
                @endif
            </div>

            <div class="p-4 w-full shrink-0 z-10 relative">
                @if (auth()->user()->role->nombre_rol === 'Administrador')
                    <a href="{{ route('inicio') }}#productos" class="block text-white text-[10px] font-bold bg-black/30 hover:bg-black/50 transition text-center py-2 rounded-xl shadow-inner uppercase tracking-wider">
                        Ver Catálogo Público &rarr;
                    </a>
                @else
                    <a href="{{ route('apicultor.productos') }}" class="block text-white text-[10px] font-bold bg-black/30 hover:bg-black/50 transition text-center py-2 rounded-xl shadow-inner uppercase tracking-wider">
                        Ir a mis productos &rarr;
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>