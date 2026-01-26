<?php

namespace App\Http\Controllers;

use App\Models\EstadoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstadoActivoController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/EstadoActivo/Index', [
            'items' => EstadoActivo::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:estados_activos']);
        EstadoActivo::create($request->all());
        return redirect()->back()->with('message', 'Estado creado con éxito');
    }

    public function update(Request $request, EstadoActivo $estadoActivo)
    {
        $request->validate(['nombre' => "required|string|max:100|unique:estados_activos,nombre,{$estadoActivo->id}"]);
        $estadoActivo->update($request->all());
        return redirect()->back()->with('message', 'Estado actualizado con éxito');
    }

    public function destroy(EstadoActivo $estadoActivo)
    {
        $estadoActivo->delete();
        return redirect()->back()->with('message', 'Estado eliminado con éxito');
    }
}
