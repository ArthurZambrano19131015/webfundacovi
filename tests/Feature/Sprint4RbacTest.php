<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Sprint4RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_apicultor_no_puede_acceder_a_gestion_de_usuarios()
    {
        // 1. Crear un Apicultor
        $rol = Role::create(['nombre_rol' => 'Apicultor']);
        $apicultor = User::create([
            'nombre_completo' => 'Juan', 
            'email' => 'juan@fundacovi.org', 
            'id_rol' => $rol->id, 
            'password' => '123'
        ]);

        // 2. Actuar como este usuario
        $this->actingAs($apicultor);

        // 3. Intentar acceder a la ruta exclusiva del Admin
        $response = $this->get('/admin/usuarios');

        // 4. Aserción: Debe devolver un código 403 (Acceso Denegado / Forbidden)
        $response->assertStatus(403);
    }
}