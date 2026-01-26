<?php

namespace App\Http\Controllers;

use App\Models\ActivoFijo;
use App\Models\Terreno;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Color;
use App\Models\Fuente;
use App\Models\Proveedor;
use App\Models\PersonalResponsable;
use App\Models\EstadoActivo;
use App\Models\Ubicacion;
use App\Models\Categoria;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TerrenoController extends Controller
{
    public function index()
    {
        return Inertia::render('Terrenos/Index', [
            'items' => Terreno::with([
                'activoFijo.marca',
                'activoFijo.modelo',
                'activoFijo.color',
                'activoFijo.fuente',
                'activoFijo.proveedor',
                'activoFijo.responsable',
                'activoFijo.estadoActivo',
                'activoFijo.ubicacion',
                'activoFijo.categoria',
                'activoFijo.departamento',
            ])->get(),
            'catalogs' => [
                'marcas' => Marca::all(),
                'modelos' => Modelo::all(),
                'colores' => Color::all(),
                'fuentes' => Fuente::all(),
                'proveedores' => Proveedor::all(),
                'responsables' => PersonalResponsable::all(),
                'estados' => EstadoActivo::all(),
                'ubicaciones' => Ubicacion::all(),
                'categorias' => Categoria::all(),
                'departamentos' => Departamento::all(),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Terrenos/Create', [
            'catalogs' => [
                'marcas' => Marca::all(),
                'modelos' => Modelo::all(),
                'colores' => Color::all(),
                'fuentes' => Fuente::all(),
                'proveedores' => Proveedor::all(),
                'responsables' => PersonalResponsable::all(),
                'estados' => EstadoActivo::all(),
                'ubicaciones' => Ubicacion::all(),
                'categorias' => Categoria::all(),
                'departamentos' => Departamento::all(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Terreno Store Request Data:', $request->all());


        $validated = $request->validate([
            // Activo Fijo
            'codigo' => 'required|string|max:50|unique:activos_fijos',
            'descripcion' => 'required|string|max:255',
            'serie' => 'nullable|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'vida_util' => 'nullable|integer|min:0',
            'valor_residual' => 'nullable|numeric|min:0',
            'marca_id' => 'nullable|exists:marcas,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'color_id' => 'nullable|exists:colores,id',
            'fuente_id' => 'required|exists:fuentes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'responsable_id' => 'required|exists:personal_responsables,id',
            'estado_activo_id' => 'required|exists:estados_activos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'imagen' => 'nullable|image|max:2048',
            // Terreno
            'area_m2' => 'required|numeric|min:0',
            'folio_real' => 'required|string|max:50',
            'catastro' => 'required|string|max:50',
            'uso' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $activoData = $request->only([
                'codigo',
                'descripcion',
                'serie',
                'categoria_id',
                'departamento_id',
                'fecha_adquisicion',
                'valor_adquisicion',
                'vida_util',
                'valor_residual',
                'marca_id',
                'modelo_id',
                'color_id',
                'fuente_id',
                'proveedor_id',
                'responsable_id',
                'estado_activo_id',
                'ubicacion_id'
            ]);

            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('activos', 'public');
                $activoData['imagen'] = $path;
            }

            $activoData['creado_por'] = Auth::id();
            $activoFijo = ActivoFijo::create($activoData);

            Terreno::create([
                'activo_fijo_id' => $activoFijo->id,
                'area_m2' => $validated['area_m2'],
                'folio_real' => $validated['folio_real'],
                'catastro' => $validated['catastro'],
                'uso' => $validated['uso'],
            ]);

            DB::commit();
            return redirect()->route('terrenos.index')->with('message', 'Terreno registrado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar el terreno: ' . $e->getMessage());
        }
    }

    public function edit(Terreno $terreno)
    {
        return Inertia::render('Terrenos/Edit', [
            'terreno' => $terreno->load('activoFijo'),
            'catalogs' => [
                'marcas' => Marca::all(),
                'modelos' => Modelo::all(),
                'colores' => Color::all(),
                'fuentes' => Fuente::all(),
                'proveedores' => Proveedor::all(),
                'responsables' => PersonalResponsable::all(),
                'estados' => EstadoActivo::all(),
                'ubicaciones' => Ubicacion::all(),
                'categorias' => Categoria::all(),
                'departamentos' => Departamento::all(),
            ]
        ]);
    }

    public function update(Request $request, Terreno $terreno)
    {
        $activoFijo = $terreno->activoFijo;

        $validated = $request->validate([
            // Activo Fijo
            'codigo' => 'required|string|max:50|unique:activos_fijos,codigo,' . $activoFijo->id,
            'descripcion' => 'required|string|max:255',
            'serie' => 'nullable|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'vida_util' => 'nullable|integer|min:0',
            'valor_residual' => 'nullable|numeric|min:0',
            'marca_id' => 'nullable|exists:marcas,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'color_id' => 'nullable|exists:colores,id',
            'fuente_id' => 'required|exists:fuentes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'responsable_id' => 'required|exists:personal_responsables,id',
            'estado_activo_id' => 'required|exists:estados_activos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'imagen' => 'nullable|image|max:2048',
            // Terreno
            'area_m2' => 'required|numeric|min:0',
            'folio_real' => 'required|string|max:50',
            'catastro' => 'required|string|max:50',
            'uso' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $activoData = $request->only([
                'codigo',
                'descripcion',
                'serie',
                'categoria_id',
                'departamento_id',
                'fecha_adquisicion',
                'valor_adquisicion',
                'vida_util',
                'valor_residual',
                'marca_id',
                'modelo_id',
                'color_id',
                'fuente_id',
                'proveedor_id',
                'responsable_id',
                'estado_activo_id',
                'ubicacion_id'
            ]);

            if ($request->hasFile('imagen')) {
                if ($activoFijo->imagen) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($activoFijo->imagen);
                }
                $path = $request->file('imagen')->store('activos', 'public');
                $activoData['imagen'] = $path;
            }

            $activoData['modificado_por'] = Auth::id();
            $activoFijo->update($activoData);

            $terreno->update([
                'area_m2' => $validated['area_m2'],
                'folio_real' => $validated['folio_real'],
                'catastro' => $validated['catastro'],
                'uso' => $validated['uso'],
            ]);

            DB::commit();
            return redirect()->route('terrenos.index')->with('message', 'Terreno actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el terreno: ' . $e->getMessage());
        }
    }

    public function destroy(Terreno $terreno)
    {
        DB::beginTransaction();
        try {
            $activoFijo = $terreno->activoFijo;

            $terreno->delete();

            if ($activoFijo->imagen) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($activoFijo->imagen);
            }
            $activoFijo->delete();

            DB::commit();
            return redirect()->back()->with('message', 'Terreno eliminado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el terreno: ' . $e->getMessage());
        }
    }
}
