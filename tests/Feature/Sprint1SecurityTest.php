<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Sprint1SecurityTest extends TestCase
{
    use RefreshDatabase; // Limpia la BD después del test

    public function test_usuario_se_crea_con_uuid_y_clave_cifrada()
    {
        $rol = Role::create(['nombre_rol' => 'Apicultor']);

        $user = User::create([
            'nombre_completo' => 'Prueba Apicultor',
            'email' => 'test@fundacovi.org',
            'id_rol' => $rol->id,
            'password' => Hash::make('ClaveSecreta123!'),
            'estado_activo' => true,
        ]);

        // Aserciones (Verificaciones)
        $this->assertNotNull($user->id_local, 'El UUID local no se generó');
        $this->assertEquals(36, strlen($user->id_local), 'El formato UUID no es válido');
        $this->assertTrue(Hash::check('ClaveSecreta123!', $user->password), 'La contraseña no fue cifrada correctamente');
    }
}