<?php

namespace App\Livewire\Admin;

use App\Models\EstandarCalidad;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class EstandarManager extends Component
{
    use WithPagination;

    public $search = '';

    public function mount()
    {
        // Seguridad: Solo Administrador
        if (auth()->user()->role->nombre_rol !== 'Administrador') {
            abort(403, 'Acceso Denegado. Solo administradores pueden configurar estándares.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $estandares = EstandarCalidad::where('parametro', 'like', '%' . $this->search . '%')
            ->orderBy('estado_activo', 'desc')
            ->paginate(6);

        return view('livewire.admin.estandar-manager',[
            'estandares' => $estandares,
        ]);
    }

    public function storeEstandar($data)
    {
        $validator = Validator::make($data,[
            'parametro' => ['required', 'string', 'unique:estandar_calidads,parametro'],
            'unidad_medida' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return['error' => 'El parámetro ya existe o los datos son inválidos.'];
        }

        EstandarCalidad::create([
            'id_local' => $data['id_local'],
            'parametro' => $data['parametro'],
            'valor_minimo' => $data['valor_minimo'] ?: null,
            'valor_maximo' => $data['valor_maximo'] ?: null,
            'unidad_medida' => $data['unidad_medida'],
            'estado_activo' => true,
        ]);
        return['success' => true];
    }

    public function updateEstandar($data)
    {
        $estandar = EstandarCalidad::where('id_local', $data['id_local'])->firstOrFail();

        $validator = Validator::make($data, [
            'parametro' =>['required', 'string', Rule::unique('estandar_calidads')->ignore($estandar->id)],
            'unidad_medida' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return ['error' => 'El parámetro ya existe o los datos son inválidos.'];
        }

        $estandar->update([
            'parametro' => $data['parametro'],
            'valor_minimo' => $data['valor_minimo'] ?: null,
            'valor_maximo' => $data['valor_maximo'] ?: null,
            'unidad_medida' => $data['unidad_medida'],
        ]);
        return ['success' => true];
    }

    public function toggleStatus($id)
    {
        $estandar = EstandarCalidad::findOrFail($id);
        $estandar->update(['estado_activo' => !$estandar->estado_activo]);
    }
}
