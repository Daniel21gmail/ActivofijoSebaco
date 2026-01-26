<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\ActivoFijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activo_fijo_id' => 'required|exists:activos_fijos,id',
            'fecha_movimiento' => 'required|date',
            'ubicacion_destino_id' => 'required|exists:ubicaciones,id',
            'responsable_destino_id' => 'required|exists:personal_responsables,id',
            'autorizado_por' => 'required|exists:users,id',
            'departamento_destino_id' => 'required|exists:departamentos,id', // Added for updating many-to-many or direct relation
        ]);

        $activo = ActivoFijo::findOrFail($validated['activo_fijo_id']);

        DB::transaction(function () use ($validated, $activo) {
            // Create movement record
            Movimiento::create([
                'activo_fijo_id' => $validated['activo_fijo_id'],
                'fecha_movimiento' => $validated['fecha_movimiento'],
                'ubicacion_origen_id' => $activo->ubicacion_id,
                'ubicacion_destino_id' => $validated['ubicacion_destino_id'],
                'responsable_origen_id' => $activo->responsable_id,
                'responsable_destino_id' => $validated['responsable_destino_id'],
                'autorizado_por' => $validated['autorizado_por'],
                'creado_por' => Auth::id(),
            ]);

            // Update asset current location/responsible
            $activo->update([
                'ubicacion_id' => $validated['ubicacion_destino_id'],
                'responsable_id' => $validated['responsable_destino_id'],
                'departamento_id' => $validated['departamento_destino_id'],
            ]);
        });

        return redirect()->back()->with('message', 'Traslado registrado con éxito');
    }
}
