<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarcaController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Marca/Index', [
            'items' => Marca::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:marcas']);
        Marca::create($request->all());
        return redirect()->back()->with('message', 'Marca creada con éxito');
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate(['nombre' => "required|string|max:100|unique:marcas,nombre,{$marca->id}"]);
        $marca->update($request->all());
        return redirect()->back()->with('message', 'Marca actualizada con éxito');
    }

    public function destroy(Marca $marca)
    {
        $marca->delete();
        return redirect()->back()->with('message', 'Marca eliminada con éxito');
    }
}
