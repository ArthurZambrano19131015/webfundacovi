<?php

namespace App\Livewire\Apicultor;

use App\Models\Cosecha;
use App\Models\Colmena;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

#[Layout('layouts.app')]
class CosechaManager extends Component
{
    use WithPagination;

    public $fecha_inicio = '';
    public $fecha_fin = '';

    public function updatingFechaInicio() { $this->resetPage(); }
    public function updatingFechaFin() { $this->resetPage(); }

    public function render()
    {
        $userId = Auth::id();

        $colmenas = Colmena::where('estado_activo', true)
            ->whereHas('apiario', function($q) use ($userId) {
                $q->where('id_apicultor', $userId);
            })->get();

        $query = Cosecha::with(['colmena.apiario'])
            ->whereHas('colmena.apiario', function($q) use ($userId) {
                $q->where('id_apicultor', $userId);
            });
            
        if ($this->fecha_inicio) {
            $query->whereDate('fecha_recoleccion', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $query->whereDate('fecha_recoleccion', '<=', $this->fecha_fin);
        }

        $cosechas = $query->orderBy('fecha_recoleccion', 'desc')->paginate(6);

        return view('livewire.apicultor.cosecha-manager',[
            'colmenas' => $colmenas,
            'cosechas' => $cosechas,
        ]);
    }

    public function storeCosecha($data)
    {
        $validator = Validator::make($data, [
            'id_colmena'        =>['required', 'exists:colmenas,id'],
            'fecha_recoleccion' =>['required', 'date', 'before_or_equal:today'],
            'cantidad_kg'       =>['required', 'numeric', 'min:0.1'],
            'novedades'         => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        Cosecha::create([
            'id_local'          => $data['id_local'],
            'id_colmena'        => $data['id_colmena'],
            'fecha_recoleccion' => $data['fecha_recoleccion'],
            'cantidad_kg'       => $data['cantidad_kg'],
            'novedades'         => $data['novedades'] ?? null,
            'synced'            => true,
        ]);

        return ['success' => true];
    }
}