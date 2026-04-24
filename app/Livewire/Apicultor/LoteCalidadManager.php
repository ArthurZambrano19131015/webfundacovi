<?php

namespace App\Livewire\Apicultor;

use App\Models\Cosecha;
use App\Models\EstandarCalidad;
use App\Models\LoteCalidad;
use App\Models\ResultadoParametro;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class LoteCalidadManager extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $userId = Auth::id();

        $cosechasDisponibles = Cosecha::with('colmena.apiario')
            ->whereNull('id_lote')
            ->whereHas('colmena.apiario', function($q) use ($userId) {
                $q->where('id_apicultor', $userId);
            })
            ->orderBy('fecha_recoleccion', 'asc')
            ->get();

        $estandares = EstandarCalidad::where('estado_activo', true)->get();

        $lotes = LoteCalidad::with(['resultados.estandar', 'cosechas'])
            ->where('id_registrado_por', $userId)
            ->where('codigo_lote', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('livewire.apicultor.lote-calidad-manager',[
            'cosechasDisponibles' => $cosechasDisponibles,
            'estandares' => $estandares,
            'lotes' => $lotes,
        ]);
    }

    public function storeLote($data)
    {
        // Iniciamos una transacción
        DB::beginTransaction();
        
        try {
            // 1. Crear el Lote
            $codigoLote = 'LOT-' . date('Y') . '-' . strtoupper(Str::random(5));
            
            $lote = LoteCalidad::create([
                'id_local' => $data['id_local'],
                'id_registrado_por' => Auth::id(),
                'codigo_lote' => $codigoLote,
                'estado_aprobacion' => $data['estado_aprobacion'],
                'synced' => true,
            ]);

            // 2. Asociar las Cosechas al Lote
            Cosecha::whereIn('id_local', $data['cosechas_ids'])->update([
                'id_lote' => $lote->id
            ]);

            // 3. Registrar los Resultados de Calidad
            foreach ($data['resultados'] as $idEstandar => $res) {
                ResultadoParametro::create([
                    'id_local' => (string) Str::uuid(),
                    'id_lote' => $lote->id,
                    'id_estandar' => $idEstandar,
                    'valor_obtenido' => $res['valor'],
                    'cumple_estandar' => $res['cumple'],
                    'synced' => true,
                ]);
            }

            DB::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return['error' => 'Error de integridad en BD: ' . $e->getMessage()];
        }
    }
}