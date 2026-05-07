<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\Cosecha;
use App\Models\Producto;

class SyncController extends Controller
{
    public function sync(Request $request)
    {
        $tabla = $request->input('tabla');
        $data = $request->input('data');

        try {
            if ($tabla === 'apiarios') {
                Apiario::updateOrCreate(
                    ['id_local' => $data['id_local']],
                    [
                        'id_apicultor' => $data['id_apicultor'],
                        'nombre' => $data['nombre'],
                        'latitud' => empty($data['latitud']) ? null : $data['latitud'],
                        'longitud'  => empty($data['longitud']) ? null : $data['longitud'],
                        'municipio'  => empty($data['municipio']) ? null : $data['municipio'],
                        'estado_activo' => $data['estado_activo'],
                        'synced' => true,
                    ]
                );
            } elseif ($tabla === 'colmenas') {
                Colmena::updateOrCreate(
                    ['id_local' => $data['id_local']],
                    [
                        'id_apiario' => $data['id_apiario'],
                        'identificador' => $data['identificador'],
                        'tipo_colmena' => empty($data['tipo_colmena']) ? null : $data['tipo_colmena'],
                        'fecha_instalacion' => $data['fecha_instalacion'],
                        'estado_activo' => $data['estado_activo'],
                        'synced' => true,
                    ]
                );
            } elseif ($tabla === 'cosechas') {
                Cosecha::updateOrCreate(
                    ['id_local' => $data['id_local']],
                    [
                        'id_colmena' => $data['id_colmena'],
                        'fecha_recoleccion' => $data['fecha_recoleccion'],
                        'cantidad_kg' => $data['cantidad_kg'],
                        'novedades' => empty($data['novedades']) ? null : $data['novedades'],
                        'synced' => true,
                    ]
                );
            } elseif ($tabla === 'productos') {
                Producto::updateOrCreate(
                    ['id_local' => $data['id_local']],
                    [
                        'id_apiario' => $data['id_apiario'],
                        'nombre' => $data['nombre'],
                        'precio' => $data['precio'],
                        'foto' => empty($data['foto_b64']) ? null : $data['foto_b64'],
                        'observaciones' => empty($data['observaciones']) ? null : $data['observaciones'],
                        'estado_activo' => $data['estado_activo'],
                        'synced' => true,
                    ]
                );
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
