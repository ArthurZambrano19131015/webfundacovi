<?php

namespace Tests\Feature;

use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class Sprint2ProduccionTest extends TestCase
{
    use RefreshDatabase;

    public function test_colmena_no_puede_duplicar_identificador_en_mismo_apiario()
    {
        $rol = Role::create(['nombre_rol' => 'Apicultor']);
        $user = User::create(['nombre_completo' => 'Api', 'email' => 'a@a.com', 'id_rol' => $rol->id, 'password' => '123']);
        
        $apiario = Apiario::create([
            'id_apicultor' => $user->id,
            'nombre' => 'Apiario Central',
            'estado_activo' => true
        ]);

        // 1. Creamos la primera colmena (Debería ser exitoso)
        Colmena::create([
            'id_apiario' => $apiario->id,
            'identificador' => 'COL-01',
            'fecha_instalacion' => '2026-05-01'
        ]);

        // 2. Esperamos que Laravel arroje una excepción SQL al intentar duplicarla
        $this->expectException(QueryException::class);

        Colmena::create([
            'id_apiario' => $apiario->id,
            'identificador' => 'COL-01', // ¡IDENTIFICADOR REPETIDO!
            'fecha_instalacion' => '2026-05-02'
        ]);
    }
}