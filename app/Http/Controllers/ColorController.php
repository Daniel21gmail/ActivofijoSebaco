<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ColorController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Color/Index', [
            'items' => Color::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:colores']);
        Color::create($request->all());
        return redirect()->back()->with('message', 'Color creado con éxito');
    }

    public function update(Request $request, Color $color)
    {
        $request->validate(['nombre' => "required|string|max:100|unique:colores,nombre,{$color->id}"]);
        $color->update($request->all());
        return redirect()->back()->with('message', 'Color actualizado con éxito');
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->back()->with('message', 'Color eliminado con éxito');
    }
}
