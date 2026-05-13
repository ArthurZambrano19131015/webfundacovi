<?php

namespace App\Livewire\Admin;

use App\Models\Apiario;
use App\Models\Colmena;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class ColmenaManager extends Component
{
    use WithPagination;

    // FILTROS
    public $search = '';
    public $filtro_apiario = '';
    public $filtro_estado = '';

    public function resetFilters()
    {
        $this->reset(['search', 'filtro_apiario', 'filtro_estado']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFiltroApiario()
    {
        $this->resetPage();
    }
    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user->role->nombre_rol === 'Administrador';

        $apiariosQuery = Apiario::where('estado_activo', true);
        if (!$isAdmin) $apiariosQuery->where('id_apicultor', $user->id);
        $apiarios = $isAdmin ? $apiariosQuery->with('apicultor')->get() : $apiariosQuery->get();

        $colmenasQuery = Colmena::with('apiario');
        if (!$isAdmin) {
            $colmenasQuery->whereHas('apiario', fn($q) => $q->where('id_apicultor', $user->id));
        }

        if ($this->search) {
            $colmenasQuery->where(function ($query) {
                $query->where('identificador', 'like', '%' . $this->search . '%')
                    ->orWhere('tipo_colmena', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->filtro_apiario) {
            $colmenasQuery->where('id_apiario', $this->filtro_apiario);
        }
        if ($this->filtro_estado !== '') {
            $colmenasQuery->where('estado_activo', $this->filtro_estado);
        }

        $hasFilters = !empty($this->search) || !empty($this->filtro_apiario) || $this->filtro_estado !== '';

        return view('livewire.admin.colmena-manager', [
            'apiarios' => $apiarios,
            'colmenas' => $colmenasQuery->orderBy('estado_activo', 'desc')->orderBy('created_at', 'desc')->paginate(6),
            'isAdmin'  => $isAdmin,
            'hasFilters' => $hasFilters
        ]);
    }

    public function storeColmena($data)
    {
        $validator = Validator::make($data, [
            'id_local'          => 'required|uuid',
            'id_apiario'        => 'required|exists:apiarios,id',
            'fecha_instalacion' => 'required|date',
            'tipo_colmena'      => 'nullable|string|max:50',
            'identificador'     => [
                'required',
                'string',
                'max:50',
                Rule::unique('colmenas')->where('id_apiario', $data['id_apiario'])
            ],
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        Colmena::create([
            'id_local'         => $data['id_local'],
            'id_apiario'       => $data['id_apiario'],
            'identificador'    => $data['identificador'],
            'tipo_colmena'     => $data['tipo_colmena'] ?? null,
            'fecha_instalacion' => $data['fecha_instalacion'],
            'estado_activo'    => true,
            'synced'           => true,
        ]);

        return ['success' => true];
    }

    public function updateColmena($data)
    {
        $colmena = Colmena::where('id_local', $data['id_local'])->firstOrFail();

        $validator = Validator::make($data, [
            'id_apiario'        => 'required|exists:apiarios,id',
            'fecha_instalacion' => 'required|date',
            'tipo_colmena'      => 'nullable|string|max:50',
            'identificador'     => [
                'required',
                'string',
                'max:50',
                Rule::unique('colmenas')
                    ->where('id_apiario', $data['id_apiario'])
                    ->ignore($colmena->id)
            ],
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        $colmena->update([
            'id_apiario'       => $data['id_apiario'],
            'identificador'    => $data['identificador'],
            'tipo_colmena'     => $data['tipo_colmena'] ?? null,
            'fecha_instalacion' => $data['fecha_instalacion'],
            'synced'           => true,
        ]);

        return ['success' => true];
    }

    public function toggleColmenaStatus($id)
    {
        $colmena = Colmena::findOrFail($id);
        $colmena->update(['estado_activo' => !$colmena->estado_activo]);
    }
}
