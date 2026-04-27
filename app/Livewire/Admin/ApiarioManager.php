<?php

namespace App\Livewire\Admin;

use App\Models\Apiario;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ApiarioManager extends Component
{
    use WithPagination;

    // FILTROS
    public $search = '';
    public $filtro_estado = '';

    public function resetFilters()
    {
        $this->reset(['search', 'filtro_estado']);
        $this->resetPage();
    }

    public function updatingSearch()
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

        $query = Apiario::query();

        if (!$isAdmin) {
            $query->where('id_apicultor', $user->id);
        } else {
            $query->with('apicultor');
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('municipio', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->filtro_estado !== '') {
            $query->where('estado_activo', $this->filtro_estado);
        }

        $hasFilters = !empty($this->search) || $this->filtro_estado !== '';

        return view('livewire.admin.apiario-manager', [
            'apiarios' => $query->orderBy('estado_activo', 'desc')->orderBy('created_at', 'desc')->paginate(6),
            'isAdmin'  => $isAdmin,
            'hasFilters' => $hasFilters
        ]);
    }

    public function storeApiario($data)
    {
        Apiario::create([
            'id_local'      => $data['id_local'] ?? \Illuminate\Support\Str::uuid(),
            'id_apicultor'  => Auth::id(),
            'nombre'        => $data['nombre'],
            'latitud'       => $data['latitud'] ?? null,
            'longitud'      => $data['longitud'] ?? null,
            'municipio'     => $data['municipio'] ?? null,
            'estado_activo' => true,
            'synced'        => true,
        ]);
        return true;
    }

    public function updateApiario($data)
    {
        $apiario = Apiario::where('id_local', $data['id_local'])->firstOrFail();
        $apiario->update([
            'nombre'        => $data['nombre'],
            'latitud'       => $data['latitud'] ?? null,
            'longitud'      => $data['longitud'] ?? null,
            'municipio'     => $data['municipio'] ?? null,
            'synced'        => true,
        ]);
        return true;
    }

    public function deleteApiario($id)
    {
        $apiario = Apiario::findOrFail($id);
        $apiario->update(['estado_activo' => !$apiario->estado_activo]);
    }
}
