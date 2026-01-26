<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartamentoController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Departamento/Index', [
            'items' => Departamento::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:departamentos']);
        Departamento::create($request->all());
        return redirect()->back()->with('message', 'Departamento creado con éxito');
    }

    public function update(Request $request, Departamento $departamento)
    {
        $request->validate(['nombre' => "required|string|max:100|unique:departamentos,nombre,{$departamento->id}"]);
        $departamento->update($request->all());
        return redirect()->back()->with('message', 'Departamento actualizado con éxito');
    }

    public function destroy(Departamento $departamento)
    {
        try {
            $departamento->delete();
            return redirect()->back()->with('message', 'Departamento eliminado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar el departamento porque tiene registros asociados.');
        }
    }
}
