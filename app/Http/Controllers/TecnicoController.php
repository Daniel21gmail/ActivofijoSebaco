<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TecnicoController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Tecnico/Index', [
            'items' => Tecnico::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20'
        ]);
        Tecnico::create($request->all());
        return redirect()->back()->with('message', 'Técnico creado con éxito');
    }

    public function update(Request $request, Tecnico $tecnico)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20'
        ]);
        $tecnico->update($request->all());
        return redirect()->back()->with('message', 'Técnico actualizado con éxito');
    }

    public function destroy(Tecnico $tecnico)
    {
        try {
            $tecnico->delete();
            return redirect()->back()->with('message', 'Técnico eliminado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar el técnico porque tiene registros asociados.');
        }
    }
}
