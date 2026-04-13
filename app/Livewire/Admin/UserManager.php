<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    public function render()
    {
        return view('livewire.admin.user-manager',[
            'usuarios' => User::with('role')->get(),
            'roles' => Role::all(),
        ]);
    }

    // Recibimos la data enviada por Alpine.js
    public function storeUser($data)
    {
        $this->validateData($data);

        User::create([
            'nombre_completo' => $data['nombre_completo'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'id_rol' => $data['id_rol'],
            'password' => Hash::make($data['password']),
            'estado_activo' => true,
        ]);
        
        session()->flash('message', 'Usuario creado exitosamente.');
    }

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->estado_activo = !$user->estado_activo;
        $user->save();
    }

    private function validateData($data)
    {
        // Reglas de validación base
        $validator = \Validator::make($data,[
            'nombre_completo' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'id_rol' => 'required|exists:roles,id',
            'password' => 'required|min:8',
        ]);

        $validator->validate();
    }
}
