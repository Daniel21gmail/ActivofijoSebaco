<?php

namespace App\Http\Controllers;

use App\Models\BajaActivo;
use App\Models\ActivoFijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BajaActivoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activo_fijo_id' => 'required|exists:activos_fijos,id|unique:bajas_activos,activo_fijo_id',
            'fecha_baja' => 'required|date',
            'motivo' => 'required|string|max:255',
            'autorizado_por' => 'required|exists:users,id',
        ]);

        $validated['creado_por'] = Auth::id();

        DB::transaction(function () use ($validated) {
            BajaActivo::create($validated);

            // Optionally update the asset status if there's a specific "Dado de Baja" status
            // $activo = ActivoFijo::find($validated['activo_fijo_id']);
            // $activo->update(['estado_activo_id' => SOME_ID]);
        });

        return redirect()->back()->with('message', 'Activo dado de baja con éxito');
    }
}
