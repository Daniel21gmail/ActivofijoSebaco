<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MantenimientoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activo_fijo_id' => 'required|exists:activos_fijos,id',
            'fecha_inicio' => 'required|date',
            'costo' => 'required|numeric|min:0',
            'proveedor_id' => 'required|exists:proveedores,id',
            'responsable_id' => 'required|exists:personal_responsables,id',
            'tipo' => 'required|string|max:50',
        ]);

        $validated['creado_por'] = Auth::id();

        Mantenimiento::create($validated);

        return redirect()->back()->with('message', 'Mantenimiento registrado con éxito');
    }

    public function print($id)
    {
        $mantenimiento = Mantenimiento::with(['activoFijo', 'proveedor', 'responsable', 'creadoPor'])->findOrFail($id);

        return Inertia::render('ActivosFijos/MantenimientoPrint', [
            'mantenimiento' => $mantenimiento
        ]);
    }
}
