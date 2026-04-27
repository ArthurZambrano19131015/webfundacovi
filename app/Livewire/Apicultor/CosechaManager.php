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

    // FILTROS
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $filtro_colmena = '';

    public function resetFilters()
    {
        $this->reset(['fecha_inicio', 'fecha_fin', 'filtro_colmena']);
        $this->resetPage();
    }

    public function updatingFechaInicio()
    {
        $this->resetPage();
    }
    public function updatingFechaFin()
    {
        $this->resetPage();
    }
    public function updatingFiltroColmena()
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();

        $colmenas = Colmena::with('apiario')->where('estado_activo', true)
            ->whereHas('apiario', fn($q) => $q->where('id_apicultor', $userId))->get();

        $query = Cosecha::with(['colmena.apiario'])
            ->whereHas('colmena.apiario', fn($q) => $q->where('id_apicultor', $userId));

        if ($this->fecha_inicio) $query->whereDate('fecha_recoleccion', '>=', $this->fecha_inicio);
        if ($this->fecha_fin) $query->whereDate('fecha_recoleccion', '<=', $this->fecha_fin);
        if ($this->filtro_colmena) $query->where('id_colmena', $this->filtro_colmena);

        $hasFilters = !empty($this->fecha_inicio) || !empty($this->fecha_fin) || !empty($this->filtro_colmena);

        return view('livewire.apicultor.cosecha-manager', [
            'colmenas' => $colmenas,
            'cosechas' => $query->orderBy('fecha_recoleccion', 'desc')->paginate(6),
            'hasFilters' => $hasFilters
        ]);
    }

    public function storeCosecha($data)
    {
        $validator = Validator::make($data, [
            'id_colmena'        => ['required', 'exists:colmenas,id'],
            'fecha_recoleccion' => ['required', 'date', 'before_or_equal:today'],
            'cantidad_kg'       => ['required', 'numeric', 'min:0.1'],
            'novedades'         => ['nullable', 'string'],
        ]);

        if ($validator->fails()) return ['error' => $validator->errors()->first()];

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
