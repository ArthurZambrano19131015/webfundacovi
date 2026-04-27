<?php

namespace App\Livewire\Apicultor;

use App\Models\Producto;
use App\Models\Apiario;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

#[Layout('layouts.app')]
class ProductoManager extends Component
{
    use WithPagination;

    // FILTROS AVANZADOS
    public $search = '';
    public $filtro_apiario = '';
    public $filtro_estado = ''; // '' = Todos, '1' = Activos, '0' = Inactivos

    // Resetear filtros
    public function resetFilters()
    {
        $this->reset(['search', 'filtro_apiario', 'filtro_estado']);
        $this->resetPage();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFiltroApiario() { $this->resetPage(); }
    public function updatingFiltroEstado() { $this->resetPage(); }

    public function render()
    {
        $userId = Auth::id();
        $apiarios = Apiario::where('id_apicultor', $userId)->get();

        $query = Producto::with('apiario')->whereHas('apiario', function($q) use ($userId) {
            $q->where('id_apicultor', $userId);
        });

        // APLICAR FILTROS
        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }
        if ($this->filtro_apiario) {
            $query->where('id_apiario', $this->filtro_apiario);
        }
        if ($this->filtro_estado !== '') {
            $query->where('estado_activo', $this->filtro_estado);
        }

        $hasFilters = !empty($this->search) || !empty($this->filtro_apiario) || $this->filtro_estado !== '';

        return view('livewire.apicultor.producto-manager',[
            'apiarios' => $apiarios,
            'productos' => $query->orderBy('created_at', 'desc')->paginate(8),
            'hasFilters' => $hasFilters 
        ]);
    }

    public function storeProducto($data)
    {
        $validator = Validator::make($data,[
            'id_apiario' => 'required|exists:apiarios,id',
            'nombre'     => 'required|string|max:100',
            'precio'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) return ['error' => $validator->errors()->first()];

        Producto::create([
            'id_local'      => $data['id_local'],
            'id_apiario'    => $data['id_apiario'],
            'nombre'        => $data['nombre'],
            'precio'        => $data['precio'],
            'foto'          => $data['foto_b64'] ?? null, // Base64 desde Alpine
            'observaciones' => $data['observaciones'] ?? null,
            'synced'        => true,
            'estado_activo' => true,
        ]);
        return['success' => true];
    }

    public function updateProducto($data)
    {
        $producto = Producto::where('id_local', $data['id_local'])->firstOrFail();
        
        $producto->update([
            'id_apiario'    => $data['id_apiario'],
            'nombre'        => $data['nombre'],
            'precio'        => $data['precio'],
            'foto'          => $data['foto_b64'] ?? $producto->foto,
            'observaciones' => $data['observaciones'] ?? null,
            'synced'        => true,
        ]);
        return ['success' => true];
    }

    public function toggleStatus($id)
    {
        $prod = Producto::findOrFail($id);
        $prod->update(['estado_activo' => !$prod->estado_activo]);
    }
}
