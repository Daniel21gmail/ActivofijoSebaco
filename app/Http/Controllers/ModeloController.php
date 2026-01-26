<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\Marca;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModeloController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Modelo/Index', [
            'items' => Modelo::with('marca')->get(),
            'marcas' => Marca::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'marca_id' => 'required|exists:marcas,id'
        ]);
        Modelo::create($request->all());
        return redirect()->back()->with('message', 'Modelo creado con éxito');
    }

    public function update(Request $request, Modelo $modelo)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'marca_id' => 'required|exists:marcas,id'
        ]);
        $modelo->update($request->all());
        return redirect()->back()->with('message', 'Modelo actualizado con éxito');
    }

    public function destroy(Modelo $modelo)
    {
        try {
            $modelo->delete();
            return redirect()->back()->with('message', 'Modelo eliminado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar el modelo porque tiene registros asociados.');
        }
    }
}
