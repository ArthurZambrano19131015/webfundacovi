<div class="scroll-smooth">

    <!-- 1. HERO SECTION -->
    <div id="inicio" class="w-full h-[70vh] bg-cover bg-center flex items-center shadow-md relative"
        style="background-image: url('{{ asset('img/hero-bg.png') }}'); background-color: #d97706; background-blend-mode: multiply;">
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
        <div class="relative z-10 px-6 md:px-12 w-full max-w-6xl mx-auto text-center md:text-left">
            <span
                class="bg-yellow-500 text-white text-xs font-black uppercase tracking-widest px-4 py-1.5 rounded-full mb-4 inline-block shadow-md">
                100% Origen Natural
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white drop-shadow-lg leading-tight mb-4">
                La Mejor Miel de <br><span class="text-yellow-400">Norte de Santander</span>
            </h1>
            <p class="text-base md:text-xl text-gray-200 font-medium max-w-2xl drop-shadow-sm mx-auto md:mx-0 mb-8">
                Descubre los productos apícolas directamente de nuestros productores locales. Miel, propóleo y polen con
                certificación de calidad.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="#productos"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:scale-105 text-center">
                    Ver Catálogo
                </a>
                <a href="#contacto"
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-md text-white border border-white/50 font-bold py-3 px-8 rounded-full shadow-lg transition text-center">
                    Contactar
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 space-y-24">

        <style>
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>

        <!-- 2. SECCIÓN PRODUCTOS-->
        <section id="productos" class="scroll-mt-20" x-data="{
            scrollNext() { this.$refs.slider.scrollBy({ left: 340, behavior: 'smooth' }); },
                scrollPrev() { this.$refs.slider.scrollBy({ left: -340, behavior: 'smooth' }); }
        }">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tight">Nuestros <span
                        class="text-yellow-600">Productos</span></h2>
                <div class="w-24 h-1 bg-yellow-500 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="relative w-full group">

                <!-- Botón Flotante Izquierdo -->
                <button @click="scrollPrev()"
                    class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1/2 z-20 bg-white text-yellow-600 p-4 rounded-full shadow-2xl border border-yellow-200 hover:bg-yellow-50 hover:scale-110 transition hidden md:block opacity-0 group-hover:opacity-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
                <!-- CONTENEDOR CARRUSEL -->
                <div x-ref="slider"
                    class="flex overflow-x-auto gap-6 pb-8 pt-4 px-4 snap-x snap-mandatory scrollbar-hide items-stretch">
                    @forelse($productos as $prod)
                        <!-- TARJETA -->
                        <div
                            class="shrink-0 w-[280px] sm:w-[320px] snap-center bg-white rounded-3xl shadow-lg border border-yellow-200 overflow-hidden flex flex-col hover:-translate-y-2 transition duration-300">
                            <div
                                class="h-56 w-full bg-lime-100 flex items-center justify-center relative overflow-hidden">
                                @if ($prod->foto)
                                    <img src="{{ $prod->foto }}"
                                        class="w-full h-full object-cover hover:scale-110 transition duration-500">
                                @else
                                    <span class="text-6xl drop-shadow-md">🍯</span>
                                @endif
                                <div
                                    class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full font-black shadow-md border border-white/20">
                                    ${{ number_format($prod->precio, 0) }} COP
                                </div>
                            </div>
                            <div class="p-6 flex flex-col flex-1 text-center">
                                <h3 class="font-black text-xl text-gray-800 leading-tight mb-2">{{ $prod->nombre }}</h3>
                                <p class="text-xs font-bold text-yellow-600 uppercase tracking-wider mb-3">📍 Apiario:
                                    {{ $prod->apiario->nombre }}</p>
                                <p class="text-sm text-gray-600 flex-1 line-clamp-3">
                                    {{ $prod->observaciones ?? 'Producto 100% natural, extraído artesanalmente.' }}
                                </p>
                                <a href="#contacto"
                                    class="mt-5 block w-full py-3 bg-yellow-50 text-yellow-700 font-bold rounded-xl border border-yellow-300 hover:bg-yellow-500 hover:text-white transition">
                                    Me interesa
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="w-full text-center py-16 bg-white rounded-3xl border border-dashed border-gray-300">
                            <span class="text-5xl">🐝</span>
                            <p class="mt-4 text-xl text-gray-500 font-bold">Pronto tendremos productos disponibles.</p>
                        </div>
                    @endforelse
                </div>
                <!-- Botón Flotante Derecho -->
                <button @click="scrollNext()"
                    class="absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-1/2 z-20 bg-white text-yellow-600 p-4 rounded-full shadow-2xl border border-yellow-200 hover:bg-yellow-50 hover:scale-110 transition hidden md:block opacity-0 group-hover:opacity-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

            </div>
            <!-- Indicador para móvil -->
            <div
                class="md:hidden text-center text-xs text-gray-400 font-bold uppercase tracking-widest mt-2 flex items-center justify-center gap-2">
                <span>&larr;</span> Desliza para ver más <span>&rarr;</span>
            </div>
        </section>

        <!-- 3. SECCIÓN APICULTORES -->
        <section id="apicultores"
            class="scroll-mt-20 bg-[#E9EFA7] -mx-6 px-6 py-16 rounded-3xl shadow-inner border border-yellow-300">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tight">Nuestros <span
                            class="text-green-700">Productores</span></h2>
                    <p class="text-gray-600 mt-2 font-medium">Conoce a las manos expertas detrás de cada gota de miel.
                    </p>
                    <div class="w-24 h-1 bg-green-500 mx-auto mt-4 rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                    @forelse($apicultores as $api)
                        <div
                            class="bg-white rounded-2xl p-6 shadow-md border border-white hover:border-green-300 transition flex items-center gap-4">
                            <img src="{{ $api->foto ? asset('storage/' . $api->foto) : asset('img/default-avatar.png') }}"
                                class="w-20 h-20 rounded-full object-cover border-4 border-green-100 shadow-sm">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $api->nombre_completo }}
                                </h3>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mt-1">Miembro
                                    FUNDACOVI</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 font-bold">Aún no hay productores
                            registrados.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- 4. FORMULARIO DE CONTACTO -->
        <section id="contacto" class="scroll-mt-20">
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-12 bg-white rounded-3xl shadow-2xl overflow-hidden border border-yellow-200">

                <div
                    class="bg-yellow-500 p-10 md:p-12 text-white flex flex-col justify-center relative overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-yellow-400 rounded-full opacity-50"></div>
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-yellow-600 rounded-full opacity-50"></div>

                    <div class="relative z-10">
                        <h2 class="text-3xl font-black mb-4">¡Hablemos!</h2>
                        <p class="text-yellow-50 mb-8 font-medium">¿Te interesa adquirir nuestros productos al por mayor
                            o tienes alguna duda? Escríbenos y nos pondremos en contacto contigo lo antes posible.</p>
                        <div class="space-y-4 font-bold">
                            <p class="flex items-center gap-3"><span class="text-2xl">📍</span> Norte de Santander,
                                Colombia</p>
                            <p class="flex items-center gap-3"><span class="text-2xl">✉️</span> info@fundacovi.org</p>
                            <p class="flex items-center gap-3"><span class="text-2xl">📱</span> +57 300 000 0000</p>
                        </div>
                    </div>
                </div>

                <div class="p-10 md:p-12 flex flex-col justify-center">
                    <form wire:submit="enviarMensaje" class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Tu
                                Nombre</label>
                            <input wire:model="nombre" type="text" placeholder="Ej: Juan Pérez"
                                class="w-full border-gray-300 bg-gray-50 rounded-xl px-4 py-3 focus:ring-yellow-500 focus:border-yellow-500 shadow-sm">
                            @error('nombre')
                                <span class="text-red-500 text-xs font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Correo
                                Electrónico</label>
                            <input wire:model="email" type="email" placeholder="Ej: juan@email.com"
                                class="w-full border-gray-300 bg-gray-50 rounded-xl px-4 py-3 focus:ring-yellow-500 focus:border-yellow-500 shadow-sm">
                            @error('email')
                                <span class="text-red-500 text-xs font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wider">Mensaje</label>
                            <textarea wire:model="mensaje" rows="4" placeholder="¿En qué podemos ayudarte?"
                                class="w-full border-gray-300 bg-gray-50 rounded-xl px-4 py-3 focus:ring-yellow-500 focus:border-yellow-500 shadow-sm resize-none"></textarea>
                            @error('mensaje')
                                <span class="text-red-500 text-xs font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-gray-900 hover:bg-black text-white font-black uppercase tracking-widest py-4 rounded-xl shadow-lg transition transform hover:-translate-y-1 flex justify-center items-center gap-2">
                            <span wire:loading.remove wire:target="enviarMensaje">Enviar Mensaje 🚀</span>
                            <span wire:loading wire:target="enviarMensaje">Enviando... ⏳</span>
                        </button>
                    </form>
                </div>

            </div>
        </section>

    </div>
</div>
