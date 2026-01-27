<?php

namespace App\Http\Controllers;

use App\Models\PersonalResponsable;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonalResponsableController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/PersonalResponsable/Index', [
            'items' => PersonalResponsable::with('cargo')->get(),
            'cargos' => Cargo::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'cargo_id' => 'required|exists:cargos,id',
            'email' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20'
        ]);
        PersonalResponsable::create($request->all());
        return redirect()->back()->with('message', 'Responsable creado con éxito');
    }

    public function update(Request $request, PersonalResponsable $personalResponsable)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'cargo_id' => 'required|exists:cargos,id',
            'email' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:20'
        ]);
        $personalResponsable->update($request->all());
        return redirect()->back()->with('message', 'Responsable actualizado con éxito');
    }

    public function destroy(PersonalResponsable $personalResponsable)
    {
        try {
            $personalResponsable->delete();
            return redirect()->back()->with('message', 'Responsable eliminado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar el responsable porque tiene registros asociados.');
        }
    }
}
