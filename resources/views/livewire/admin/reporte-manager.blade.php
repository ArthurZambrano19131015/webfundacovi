<div x-data="{
    chart: null,
    chartTitle: 'Cargando gráfico...',
    openExt: false, // ESTADO DEL MENÚ MOVIDO A LA RAÍZ PARA QUE NO SE CIERRE SOLO
    
    initChart(data) {
        if (!data || !data.type) return;

        if (typeof window.Chart === 'undefined') {
            setTimeout(() => this.initChart(data), 100);
            return;
        }

        // Actualizamos el título dinámico
        this.chartTitle = data.labelName;

        const canvasEl = document.getElementById('reportChart');
        if (!canvasEl) return;

        if(this.chart) {
            this.chart.destroy();
        }

        const ctx = canvasEl.getContext('2d');
        
        const bgColors = data.type === 'doughnut' 
            ?['#22c55e', '#ef4444', '#eab308', '#3b82f6', '#a855f7', '#f97316']
            : (data.type === 'line' ? 'rgba(234, 179, 8, 0.4)' : '#eab308');

        this.chart = new window.Chart(ctx, {
            type: data.type,
            data: {
                labels: data.labels,
                datasets:[{
                    label: data.labelName,
                    data: data.dataset,
                    backgroundColor: bgColors,
                    borderColor: data.type === 'line' ? '#ca8a04' : (data.type === 'bar' ? '#ca8a04' : '#ffffff'),
                    borderWidth: 2,
                    tension: 0.4,
                    fill: data.type === 'line' ? true : false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, 
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, display: data.type !== 'doughnut' }
                }
            }
        });
    },

    exportarPDF() {
        if (typeof window.html2pdf === 'undefined') {
            this.$dispatch('notify', { message: 'El generador de PDF aún no ha cargado.', type: 'warning' });
            return;
        }

        this.$dispatch('notify', { message: 'Generando PDF, por favor espera...', type: 'info' });
        const element = document.getElementById('areaImprimible');
        
        const opt = {
            margin:       0.3,
            filename:     'Reporte_Rendimiento_FUNDACOVI.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
        };
        
        window.html2pdf().set(opt).from(element).save().then(() => {
            this.$dispatch('notify', { message: 'PDF descargado exitosamente', type: 'success' });
        });
    }
}" 
x-init="initChart(@js($chartDataInicial));"
@actualizar-grafico.window="initChart($event.detail.chartData)">

    <!-- HEADER & BOTONES -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Analítica Avanzada</h2>
            <p class="text-sm font-bold text-yellow-600">Generador de Reportes Gerenciales</p>
        </div>

        <button @click="exportarPDF()" class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg shadow-md transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Exportar Informe PDF
        </button>
    </div>

    <!-- PANEL DE CONTROL (FILTROS Y LIMPIEZA) -->
    <div class="bg-white rounded-2xl shadow-sm border border-yellow-200 p-5 mb-6 relative z-30">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Configuración del Reporte</h3>
            
            <button wire:click="limpiarFiltros" class="flex items-center gap-1 text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-md transition border border-red-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Restablecer Filtros
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            
            <!-- TIPO DE ANÁLISIS -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-gray-700 mb-1">Métrica a Analizar</label>
                <select wire:model.live="tipo_analisis" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500 font-bold text-blue-700 bg-blue-50">
                    <optgroup label="Producción Agrícola">
                        <option value="produccion_tiempo">📈 Curva de Producción en el Tiempo (Kg)</option>
                        <option value="produccion_municipio">📊 Producción Total por Municipios (Kg)</option>
                        <option value="cosechas_colmena">🐝 Cosechas por Colmena (Rendimiento)</option>
                    </optgroup>
                    <optgroup label="Calidad y Normativa">
                        <option value="calidad_lotes">🥧 Tasa de Calidad de Lotes</option>
                        <option value="fallos_calidad">⚠️ Indicadores con más fallos</option>
                    </optgroup>
                    <optgroup label="Comercial y Sistema">
                        <option value="productos_oferta">🛍️ Cantidad de Productos por Apiario</option>
                        <option value="usuarios_registrados">👤 Crecimiento de Usuarios Registrados</option>
                    </optgroup>
                </select>
            </div>

            <!-- AGRUPACIÓN (Aparece solo si es gráfico de línea temporal) -->
            @if(in_array($tipo_analisis,['produccion_tiempo', 'usuarios_registrados']))
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Agrupar por</label>
                <select wire:model.live="agrupacion_tiempo" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                    <option value="mes">Por Mes</option>
                    <option value="dia">Por Día</option>
                </select>
            </div>
            @endif

            <!-- FECHAS -->
            <div class="{{ in_array($tipo_analisis, ['produccion_tiempo', 'usuarios_registrados']) ? '' : 'lg:col-start-3' }}">
                <label class="block text-xs font-bold text-gray-700 mb-1">Desde</label>
                <input wire:model.live="fecha_inicio" type="date" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Hasta</label>
                <input wire:model.live="fecha_fin" type="date" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
            </div>

            <!-- FILTROS AVANZADOS -->
            <div class="relative">
                <button @click="openExt = !openExt" class="w-full bg-gray-50 border border-gray-300 text-gray-700 py-2 rounded-md text-sm hover:bg-gray-100 flex justify-between items-center px-3 mt-5">
                    Filtros Adicionales <span>▼</span>
                </button>
                
                <div x-show="openExt" @click.away="openExt = false" style="display:none;" x-transition class="absolute right-0 mt-1 w-64 bg-white shadow-2xl rounded-lg border border-gray-200 p-4 z-50">
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Municipio</label>
                        <select wire:model.live="municipio" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                            <option value="">Todos los municipios</option>
                            @foreach($municipiosDisponibles as $mun) <option value="{{ $mun }}">{{ $mun }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Productor</label>
                        <select wire:model.live="id_apicultor" class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500">
                            <option value="">Todos los productores</option>
                            @foreach($apicultoresDisponibles as $api) <option value="{{ $api->id }}">{{ $api->nombre_completo }}</option> @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AREA IMPRIMIBLE -->
    <div id="areaImprimible" class="bg-white rounded-3xl shadow-xl border border-gray-200 p-8 relative z-10">
        
        <div class="flex justify-between items-center border-b-2 border-yellow-500 pb-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="bg-yellow-400 p-3 rounded-xl"><span class="text-3xl">🍯</span></div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 uppercase">REPORTE GERENCIAL</h1>
                    <p class="text-sm text-gray-500 font-medium">Fundación Colombia de Vida (FUNDACOVI)</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Fecha de Emisión</p>
                <p class="text-lg font-bold text-gray-800">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <h4 class="text-l font-black text-center text-gray-800 mb-6 uppercase tracking-widest">RESUMEN GENERAL</h4>
        

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-lime-50 p-4 rounded-2xl border border-lime-200 text-center">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Volumen Histórico</p>
                <p class="text-3xl font-black text-lime-700">{{ number_format($kpi_total_kg, 1) }} <span class="text-sm font-bold">kg</span></p>
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-200 text-center">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Lotes Procesados</p>
                <p class="text-3xl font-black text-blue-700">{{ $kpi_total_lotes }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-2xl border border-green-200 text-center">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Lotes Aprobados</p>
                <p class="text-3xl font-black text-green-700">{{ $kpi_aprobados }}</p>
            </div>
        </div>
        <!-- TÍTULO DINÁMICO DEL GRÁFICO -->
        <h3 x-text="chartTitle" class="text-xl font-black text-center text-gray-800 mb-6 uppercase tracking-widest"></h3>

        <div wire:ignore class="w-full h-96 relative mt-8">
            <canvas id="reportChart"></canvas>
        </div>
        
    </div>
</div>