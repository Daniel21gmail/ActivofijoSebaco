<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Proveedor/Index', [
            'items' => Proveedor::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'nit' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255'
        ], [], [
            'nit' => 'Número RUC',
            'telefono' => 'Teléfono',
            'direccion' => 'Dirección'
        ]);
        Proveedor::create($request->all());
        return redirect()->back()->with('message', 'Proveedor creado con éxito');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'nit' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255'
        ], [], [
            'nit' => 'Número RUC',
            'telefono' => 'Teléfono',
            'direccion' => 'Dirección'
        ]);
        $proveedor->update($request->all());
        return redirect()->back()->with('message', 'Proveedor actualizado con éxito');
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            return redirect()->back()->with('message', 'Proveedor eliminado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar el proveedor porque tiene registros asociados (activos o mantenimientos).');
        }
    }
}
