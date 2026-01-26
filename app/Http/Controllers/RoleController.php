<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Role/Index', [
            'items' => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles',
            'descripcion' => 'nullable|string|max:255',
        ]);
        Role::create($request->all());
        return redirect()->back()->with('message', 'Rol creado con éxito');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nombre' => "required|string|max:50|unique:roles,nombre,{$role->id}",
            'descripcion' => 'nullable|string|max:255',
        ]);
        $role->update($request->all());
        return redirect()->back()->with('message', 'Rol actualizado con éxito');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->back()->with('message', 'Rol eliminado con éxito');
    }
}
