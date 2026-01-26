<?php

namespace App\Http\Controllers;

use App\Models\Fuente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FuenteController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Fuente/Index', [
            'items' => Fuente::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:fuentes']);
        Fuente::create($request->all());
        return redirect()->back()->with('message', 'Fuente creada con éxito');
    }

    public function update(Request $request, Fuente $fuente)
    {
        $request->validate(['nombre' => "required|string|max:100|unique:fuentes,nombre,{$fuente->id}"]);
        $fuente->update($request->all());
        return redirect()->back()->with('message', 'Fuente actualizada con éxito');
    }

    public function destroy(Fuente $fuente)
    {
        $fuente->delete();
        return redirect()->back()->with('message', 'Fuente eliminada con éxito');
    }
}
