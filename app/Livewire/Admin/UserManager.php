<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class UserManager extends Component
{
    use WithFileUploads;

    public $foto; 

    public function render()
    {
        return view('livewire.admin.user-manager',[
            'usuarios' => User::with('role')->get(),
            'roles' => Role::all(),
        ]);
    }
    public function mount()
    {
        if (auth()->user()->role->nombre_rol !== 'Administrador') {
            abort(403, 'Acceso Denegado. Solo administradores pueden gestionar usuarios.');
        }
    }

    public function storeUser($data)
    {
        Validator::make($data,[
            'nombre_completo' => ['required', 'string', 'max:100'],
            'email'           =>['required', 'email', 'max:100', 'unique:users,email'],
            'telefono'        =>['nullable', 'string', 'max:20'],
            'id_rol'          =>['required', 'exists:roles,id'],
            'password'        =>['required', Password::min(8)->mixedCase()->numbers()->symbols()],
        ])->validate();

        $this->validate(['foto' => 'nullable|image|max:2048']); // Máximo 2MB

        // Guardar foto si existe
        $rutaFoto = $this->foto ? $this->foto->store('usuarios', 'public') : null;

        User::create([
            'nombre_completo' => $data['nombre_completo'],
            'email'           => $data['email'],
            'telefono'        => $data['telefono'],
            'id_rol'          => $data['id_rol'],
            'foto'            => $rutaFoto,
            'password'        => Hash::make($data['password']),
            'estado_activo'   => true,
        ]);

        $this->reset('foto'); 
    }

    public function updateUser($data)
    {
        Validator::make($data, [
            'id'              => ['required', 'exists:users,id'],
            'nombre_completo' => ['required', 'string', 'max:100'],
            'email'           =>['required', 'email', 'max:100', Rule::unique('users')->ignore($data['id'])],
            'telefono'        =>['nullable', 'string', 'max:20'],
            'id_rol'          => ['required', 'exists:roles,id'],
            'password'        => ['nullable', Password::min(8)->mixedCase()->numbers()->symbols()],
        ])->validate();

        $user = User::findOrFail($data['id']);
        
        $user->nombre_completo = $data['nombre_completo'];
        $user->email           = $data['email'];
        $user->telefono        = $data['telefono'];
        $user->id_rol          = $data['id_rol'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($this->foto) {
            $this->validate(['foto' => 'image|max:2048']);
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $this->foto->store('usuarios', 'public');
        }

        $user->save();
        $this->reset('foto');
    }

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->estado_activo = !$user->estado_activo;
        $user->save();
    }
}