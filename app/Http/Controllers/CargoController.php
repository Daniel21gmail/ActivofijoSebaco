<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CargoController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Cargo/Index', [
            'items' => Cargo::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:cargos']);
        Cargo::create($request->all());
        return redirect()->back()->with('message', 'Cargo creado con éxito');
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate(['nombre' => "required|string|max:100|unique:cargos,nombre,{$cargo->id}"]);
        $cargo->update($request->all());
        return redirect()->back()->with('message', 'Cargo actualizado con éxito');
    }

    public function destroy(Cargo $cargo)
    {
        try {
            $cargo->delete();
            return redirect()->back()->with('message', 'Cargo eliminado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar el cargo porque tiene registros asociados.');
        }
    }
}
