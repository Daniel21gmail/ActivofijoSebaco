<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoriaController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Categoria/Index', [
            'items' => Categoria::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:categorias',
            'subcategoria' => 'required|string|max:100',
            'clase' => 'required|string|max:10',
            'nombre' => 'required|string|max:100'
        ]);
        Categoria::create($request->all());
        return redirect()->back()->with('message', 'Categoría creada con éxito');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'codigo' => "required|string|max:50|unique:categorias,codigo,{$categoria->id}",
            'subcategoria' => 'required|string|max:100',
            'clase' => 'required|string|max:10',
            'nombre' => 'required|string|max:100'
        ]);
        $categoria->update($request->all());
        return redirect()->back()->with('message', 'Categoría actualizada con éxito');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->back()->with('message', 'Categoría eliminada con éxito');
    }
}
