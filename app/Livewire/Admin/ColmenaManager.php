<?php

namespace App\Livewire\Admin;

use App\Models\Apiario;
use App\Models\Colmena;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ColmenaManager extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function storeColmena($data)
    {
        // 1. Validar que el identificador sea único EN ESE APIARIO
        $validator = Validator::make($data, [
            'identificador' => [
                'required',
                Rule::unique('colmenas')->where('id_apiario', $data['id_apiario'])
            ]
        ]);

        if ($validator->fails()) {
            return ['error' => 'Ya existe una colmena con este ID en el apiario seleccionado.'];
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
            'identificador' => [
                'required',
                Rule::unique('colmenas')
                    ->where('id_apiario', $data['id_apiario'])
                    ->ignore($colmena->id)
            ]
        ]);

        if ($validator->fails()) {
            return ['error' => 'Ya existe otra colmena con este ID en el apiario seleccionado.'];
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

    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user->role->nombre_rol === 'Administrador';

        $apiariosQuery = Apiario::where('estado_activo', true);
        if (!$isAdmin) {
            $apiariosQuery->where('id_apicultor', $user->id);
        }
        $apiarios = $isAdmin ? $apiariosQuery->with('apicultor')->get() : $apiariosQuery->get();

        $colmenasQuery = Colmena::with('apiario');
        if (!$isAdmin) {
            $colmenasQuery->whereHas('apiario', function ($q) use ($user) {
                $q->where('id_apicultor', $user->id);
            });
        }

        $colmenas = $colmenasQuery->where(function ($query) {
            $query->where('identificador', 'like', '%' . $this->search . '%')
                ->orWhere('tipo_colmena', 'like', '%' . $this->search . '%');
        })
            ->orderBy('estado_activo', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('livewire.admin.colmena-manager', [
            'apiarios' => $apiarios,
            'colmenas' => $colmenas,
            'isAdmin'  => $isAdmin,
        ]);
    }
}
