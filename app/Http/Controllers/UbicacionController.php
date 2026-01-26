<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UbicacionController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Ubicacion/Index', [
            'items' => Ubicacion::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:255'
        ]);
        Ubicacion::create($request->all());
        return redirect()->back()->with('message', 'Ubicación creada con éxito');
    }

    public function update(Request $request, Ubicacion $ubicacion)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:255'
        ]);
        $ubicacion->update($request->all());
        return redirect()->back()->with('message', 'Ubicación actualizada con éxito');
    }

    public function destroy(Ubicacion $ubicacion)
    {
        try {
            $ubicacion->delete();
            return redirect()->back()->with('message', 'Ubicación eliminada con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar la ubicación porque tiene registros asociados.');
        }
    }
}
