<?php

namespace App\Livewire;

use App\Models\Apiario;
use App\Models\Cosecha;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.dashboard')] 
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        $apiarios = Apiario::where('id_apicultor', $user->id)->get();
        $municipios = $apiarios->pluck('municipio')->filter()->unique()->implode(', ');
        
        $miProduccion = Cosecha::whereHas('colmena.apiario', function($q) use ($user) {
            $q->where('id_apicultor', $user->id);
        })->sum('cantidad_kg');

        $produccionTotal = Cosecha::sum('cantidad_kg');
        $porcentajeAporte = $produccionTotal > 0 ? round(($miProduccion / $produccionTotal) * 100, 1) : 0;

        return view('livewire.dashboard',[
            'user' => $user,
            'apiariosCount' => $apiarios->count(),
            'municipios' => $municipios,
            'miProduccion' => $miProduccion,
            'porcentajeAporte' => $porcentajeAporte,
        ]);
    }
}