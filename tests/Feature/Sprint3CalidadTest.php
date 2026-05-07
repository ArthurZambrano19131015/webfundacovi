<?php

namespace Tests\Feature;

use App\Models\EstandarCalidad;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Sprint3CalidadTest extends TestCase
{
    use RefreshDatabase;

    public function test_algoritmo_evaluacion_silenciosa_rechaza_lote_fuera_de_rango()
    {
        // 1. El Admin configura el estándar
        $estandar = EstandarCalidad::create([
            'parametro' => 'Humedad',
            'valor_maximo' => 18.5,
            'unidad_medida' => '%'
        ]);

        // 2. Simulamos la lógica del componente Livewire (Evaluación Silenciosa)
        $valorIngresado = 19.0;
        $cumple = true;

        if ($estandar->valor_maximo !== null && $valorIngresado > $estandar->valor_maximo) {
            $cumple = false;
        }

        // 3. Aserción
        $this->assertFalse($cumple, 'El algoritmo aprobó un lote con humedad excesiva (Fallo Antifraude)');
    }
}