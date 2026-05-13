<?php

namespace App\Livewire;

use App\Models\Apiario;
use App\Models\Cosecha;
use App\Models\Producto; 
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user->role->nombre_rol === 'Administrador';

        // 1. Ubicaciones
        $apiariosQuery = Apiario::query();
        if (!$isAdmin) {
            $apiariosQuery->where('id_apicultor', $user->id);
        }
        $apiarios = $apiariosQuery->get();

        $municipios = $apiarios->pluck('municipio')->filter()->unique()->implode(', ');
        if (empty($municipios)) $municipios = 'Aún no has registrado ubicaciones';
    
        $apiariosMap = $apiarios->filter(function ($apiario) {
            return !empty($apiario->latitud) && !empty($apiario->longitud);
        })->map(function ($apiario) use ($isAdmin) {
            return[
                'lat' => $apiario->latitud,
                'lng' => $apiario->longitud,
                'nombre' => $apiario->nombre,
                'productor' => $isAdmin ? $apiario->apicultor->nombre_completo : 'Mi Apiario',
            ];
        })->values()->toArray();
        // ---------------------------------

        // 2. Producción
        $miProduccion = Cosecha::whereHas('colmena.apiario', function($q) use ($user) {
            $q->where('id_apicultor', $user->id);
        })->sum('cantidad_kg');

        $produccionTotal = Cosecha::sum('cantidad_kg');
        $porcentajeAporte = $produccionTotal > 0 ? round(($miProduccion / $produccionTotal) * 100, 1) : 0;

        // 3. PRODUCTOS 
        $queryProductos = Producto::where('estado_activo', true);
        if (!$isAdmin) {
            $queryProductos->whereHas('apiario', function($q) use ($user) {
                $q->where('id_apicultor', $user->id);
            });
        }
        $productos = $queryProductos->orderBy('created_at', 'desc')->take(10)->get();

        return view('livewire.dashboard',[
            'user' => $user,
            'apiariosCount' => $apiarios->count(),
            'municipios' => $municipios,
            'miProduccion' => $miProduccion,
            'porcentajeAporte' => $porcentajeAporte,
            'productos' => $productos, 
            'apiariosMap' => $apiariosMap,
        ]);
    }
}