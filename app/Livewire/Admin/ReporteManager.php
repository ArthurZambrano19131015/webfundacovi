<?php

namespace App\Livewire\Admin;

use App\Models\Cosecha;
use App\Models\LoteCalidad;
use App\Models\Apiario;
use App\Models\User;
use App\Models\ResultadoParametro;
use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app')]
class ReporteManager extends Component
{
    // Filtros
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $municipio = '';
    public $id_apicultor = '';

    // Configuración del Gráfico
    public $tipo_analisis = 'produccion_tiempo';
    public $agrupacion_tiempo = 'mes'; 

    public $municipiosDisponibles = [];
    public $apicultoresDisponibles = [];

    public function mount()
    {
        if (auth()->user()->role->nombre_rol !== 'Administrador') {
            abort(403, 'Acceso Denegado.');
        }

        // Por defecto, mostrar los últimos 6 meses
        $this->fecha_inicio = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->fecha_fin = Carbon::now()->format('Y-m-d');

        $this->municipiosDisponibles = Apiario::pluck('municipio')->filter()->unique();
        $this->apicultoresDisponibles = User::whereHas('role', fn($q) => $q->where('nombre_rol', 'Apicultor'))->get();
    }

    public function updated($property)
    {
        $this->dispatch('actualizar-grafico', chartData: $this->generarDatos());
    }

    public function limpiarFiltros()
    {
        $this->fecha_inicio = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->fecha_fin = Carbon::now()->format('Y-m-d');
        $this->municipio = '';
        $this->id_apicultor = '';
        $this->tipo_analisis = 'produccion_tiempo';
        $this->agrupacion_tiempo = 'mes';

        $this->dispatch('actualizar-grafico', chartData: $this->generarDatos());
    }

    public function generarDatos()
    {
        $queryCosechas = Cosecha::query()->with('colmena.apiario');
        $queryLotes = LoteCalidad::query();

        // APLICAR FILTROS GLOBALES
        if ($this->fecha_inicio) {
            $queryCosechas->whereDate('fecha_recoleccion', '>=', $this->fecha_inicio);
            $queryLotes->whereDate('created_at', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $queryCosechas->whereDate('fecha_recoleccion', '<=', $this->fecha_fin);
            $queryLotes->whereDate('created_at', '<=', $this->fecha_fin);
        }
        if ($this->id_apicultor) {
            $queryCosechas->whereHas('colmena.apiario', fn($q) => $q->where('id_apicultor', $this->id_apicultor));
            $queryLotes->where('id_registrado_por', $this->id_apicultor);
        }
        if ($this->municipio) {
            $queryCosechas->whereHas('colmena.apiario', fn($q) => $q->where('municipio', $this->municipio));
        }

        // 1. CURVA DE PRODUCCIÓN 
        if ($this->tipo_analisis === 'produccion_tiempo') {
            $formato = $this->agrupacion_tiempo === 'dia' ? '%Y-%m-%d' : '%Y-%m';
            $datos = $queryCosechas->selectRaw("DATE_FORMAT(fecha_recoleccion, '$formato') as periodo, SUM(cantidad_kg) as total")
                ->groupBy('periodo')->orderBy('periodo')->get();
            return ['type' => 'line', 'labels' => $datos->pluck('periodo')->toArray(), 'dataset' => $datos->pluck('total')->toArray(), 'labelName' => 'Kg Producidos'];
        }

        // 2. PRODUCCIÓN POR MUNICIPIO
        if ($this->tipo_analisis === 'produccion_municipio') {
            $datos = $queryCosechas->get()->groupBy('colmena.apiario.municipio')->map->sum('cantidad_kg');
            return ['type' => 'bar', 'labels' => array_values($datos->keys()->toArray()), 'dataset' => array_values($datos->values()->toArray()), 'labelName' => 'Kg Totales por Municipio'];
        }

        // 3. TASA DE CALIDAD 
        if ($this->tipo_analisis === 'calidad_lotes') {
            $datos = $queryLotes->select('estado_aprobacion', DB::raw('count(*) as total'))->groupBy('estado_aprobacion')->get();
            $labels = $datos->map(fn($item) => $item->estado_aprobacion . ' (' . $item->total . ')')->toArray();
            return ['type' => 'doughnut', 'labels' => $labels, 'dataset' => $datos->pluck('total')->toArray(), 'labelName' => 'Distribución de Lotes'];
        }

        // 4. CRECIMIENTO DE USUARIOS
        if ($this->tipo_analisis === 'usuarios_registrados') {
            $queryUsers = User::query();
            if ($this->fecha_inicio) $queryUsers->whereDate('created_at', '>=', $this->fecha_inicio);
            if ($this->fecha_fin) $queryUsers->whereDate('created_at', '<=', $this->fecha_fin);

            $formato = $this->agrupacion_tiempo === 'dia' ? '%Y-%m-%d' : '%Y-%m';
            $datos = $queryUsers->selectRaw("DATE_FORMAT(created_at, '$formato') as periodo, count(*) as total")->groupBy('periodo')->orderBy('periodo')->get();
            return ['type' => 'line', 'labels' => $datos->pluck('periodo')->toArray(), 'dataset' => $datos->pluck('total')->toArray(), 'labelName' => 'Nuevos Usuarios'];
        }

        // 5. COSECHAS POR COLMENA
        if ($this->tipo_analisis === 'cosechas_colmena') {
            $datos = $queryCosechas->get()->groupBy('colmena.identificador')->map->sum('cantidad_kg');
            return ['type' => 'bar', 'labels' => array_values($datos->keys()->toArray()), 'dataset' => array_values($datos->values()->toArray()), 'labelName' => 'Kg por Colmena'];
        }

        // 6. INDICADORES QUE MÁS FALLAN
        if ($this->tipo_analisis === 'fallos_calidad') {
            $queryResultados = ResultadoParametro::with('estandar')->where('cumple_estandar', false);
            if ($this->fecha_inicio) $queryResultados->whereDate('created_at', '>=', $this->fecha_inicio);
            if ($this->fecha_fin) $queryResultados->whereDate('created_at', '<=', $this->fecha_fin);

            $datos = $queryResultados->get()->groupBy('estandar.parametro')->map->count();
            return ['type' => 'bar', 'labels' => array_values($datos->keys()->toArray()), 'dataset' => array_values($datos->values()->toArray()), 'labelName' => 'Cantidad de Fallos en Laboratorio'];
        }

        // 7. CATÁLOGO DE PRODUCTOS 
        if ($this->tipo_analisis === 'productos_oferta') {
            $queryProd = Producto::with('apiario')->where('estado_activo', true);
            if ($this->municipio) $queryProd->whereHas('apiario', fn($q) => $q->where('municipio', $this->municipio));
            if ($this->id_apicultor) $queryProd->whereHas('apiario', fn($q) => $q->where('id_apicultor', $this->id_apicultor));

            $datos = $queryProd->get()->groupBy('apiario.nombre')->map->count();
            $labels = $datos->map(fn($val, $key) => $key . ' (' . $val . ')')->toArray();
            return ['type' => 'doughnut', 'labels' => array_values($labels), 'dataset' => array_values($datos->values()->toArray()), 'labelName' => 'Productos Ofertados por Apiario'];
        }

        return ['type' => 'bar', 'labels' => [], 'dataset' => [], 'labelName' => 'Sin datos'];
    }

    public function render()
    {
        return view('livewire.admin.reporte-manager', [
            'chartDataInicial' => $this->generarDatos(),
            'kpi_total_kg' => Cosecha::sum('cantidad_kg'),
            'kpi_total_lotes' => LoteCalidad::count(),
            'kpi_aprobados' => LoteCalidad::where('estado_aprobacion', 'APROBADO')->count(),
        ]);
    }
}
