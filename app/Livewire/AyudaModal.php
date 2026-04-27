<?php

namespace App\Livewire;

use App\Models\GuiaInformativa;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AyudaModal extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.ayuda-modal', [
            'guias' => GuiaInformativa::with('autor')->orderBy('created_at', 'desc')->paginate(3),
            'isAdmin' => Auth::user() && Auth::user()->role->nombre_rol === 'Administrador'
        ]);
    }

    public function storeGuia($data)
    {
        if (Auth::user()->role->nombre_rol !== 'Administrador') return;

        GuiaInformativa::create([
            'id_local'  => $data['id_local'],
            'id_autor'  => Auth::id(),
            'titulo'    => $data['titulo'],
            'contenido' => $data['contenido'],
            'synced'    => true,
        ]);
    }

    public function updateGuia($data)
    {
        if (Auth::user()->role->nombre_rol !== 'Administrador') return;

        $guia = GuiaInformativa::where('id_local', $data['id_local'])->firstOrFail();
        $guia->update([
            'titulo'    => $data['titulo'],
            'contenido' => $data['contenido'],
            'synced'    => true,
        ]);
    }

    public function deleteGuia($id)
    {
        if (Auth::user()->role->nombre_rol === 'Administrador') {
            GuiaInformativa::findOrFail($id)->delete();
        }
    }
}
