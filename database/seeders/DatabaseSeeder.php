<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear los Roles base
        $rolAdmin = Role::firstOrCreate(['nombre_rol' => 'Administrador']);
        $rolApicultor = Role::firstOrCreate(['nombre_rol' => 'Apicultor']);

        // 2. Crear el primer usuario Administrador
        User::firstOrCreate(
            ['email' => 'admin@fundacovi.org'],[
                'id_rol' => $rolAdmin->id,
                'nombre_completo' => 'Admin FUNDACOVI',
                'telefono' => '3000000000',
                'password' => Hash::make('password123'), // Cambiar en producción
                'estado_activo' => true,
            ]
        );
    }
}
