<?php

namespace App\Http\Controllers;

use App\Models\ActivoFijo;
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
use Illuminate\Support\Facades\Storage;

class ActivoFijoController extends Controller
{
    public function index()
    {
        return Inertia::render('ActivosFijos/Index', [
            'items' => ActivoFijo::with([
                'marca',
                'modelo',
                'color',
                'fuente',
                'proveedor',
                'responsable',
                'estadoActivo',
                'ubicacion',
                'categoria',
                'departamento',
                'terreno',
                'vehiculo',
                'movimientos.ubicacionOrigen',
                'movimientos.ubicacionDestino',
                'movimientos.responsableOrigen',
                'movimientos.responsableDestino',
                'movimientos.autorizadoPor',
                'mantenimientos.proveedor',
                'mantenimientos.responsable',
                'bajaActivo.autorizadoPor'
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
                'usuarios' => \App\Models\User::all(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'serie' => 'nullable|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'vida_util' => 'required|integer|min:0',
            'valor_residual' => 'required|numeric|min:0',
            'marca_id' => 'nullable|exists:marcas,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'color_id' => 'nullable|exists:colores,id',
            'fuente_id' => 'required|exists:fuentes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'responsable_id' => 'required|exists:personal_responsables,id',
            'estado_activo_id' => 'required|exists:estados_activos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('activos', 'public');
            $validated['imagen'] = $path;
        }

        // Generate automatic code
        $categoria = Categoria::findOrFail($request->categoria_id);
        $year = date('y');
        $seq = ActivoFijo::where('categoria_id', $request->categoria_id)
            ->whereYear('created_at', date('Y'))
            ->count() + 1;

        $validated['codigo'] = "{$year}-{$categoria->codigo}-" . str_pad($seq, 2, '0', STR_PAD_LEFT);
        $validated['creado_por'] = Auth::id();

        ActivoFijo::create($validated);

        return redirect()->back()->with('message', 'Activo fijo registrado con éxito');
    }

    public function update(Request $request, ActivoFijo $activoFijo)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'serie' => 'nullable|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'vida_util' => 'required|integer|min:0',
            'valor_residual' => 'required|numeric|min:0',
            'marca_id' => 'nullable|exists:marcas,id',
            'modelo_id' => 'nullable|exists:modelos,id',
            'color_id' => 'nullable|exists:colores,id',
            'fuente_id' => 'required|exists:fuentes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'responsable_id' => 'required|exists:personal_responsables,id',
            'estado_activo_id' => 'required|exists:estados_activos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            // Delete old image if exists
            if ($activoFijo->imagen) {
                Storage::disk('public')->delete($activoFijo->imagen);
            }
            $path = $request->file('imagen')->store('activos', 'public');
            $validated['imagen'] = $path;
        }

        $validated['modificado_por'] = Auth::id();

        $activoFijo->update($validated);

        return redirect()->back()->with('message', 'Activo fijo actualizado con éxito');
    }

    public function destroy(ActivoFijo $activoFijo)
    {
        try {
            if ($activoFijo->imagen) {
                Storage::disk('public')->delete($activoFijo->imagen);
            }
            $activoFijo->delete();
            return redirect()->back()->with('message', 'Activo fijo eliminado con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se puede eliminar el activo porque tiene registros relacionados.');
        }
    }

    public function print(ActivoFijo $activoFijo)
    {
        $item = $activoFijo->load([
            'marca',
            'modelo',
            'color',
            'fuente',
            'proveedor',
            'responsable',
            'estadoActivo',
            'ubicacion',
            'categoria',
            'departamento',
            'movimientos.ubicacionOrigen',
            'movimientos.ubicacionDestino',
            'movimientos.responsableOrigen',
            'movimientos.responsableDestino',
            'movimientos.autorizadoPor',
            'mantenimientos.proveedor',
            'mantenimientos.responsable',
            'bajaActivo.autorizadoPor'
        ]);

        return Inertia::render('ActivosFijos/Print', [
            'item' => $item,
            'qrCode' => base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(200)
                ->generate(config('app.url') . route('activos-fijos.print', $activoFijo->id, false)))
        ]);
    }

    public function getNextCode(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);

        // Count existing assets in this category to determine the sequence
        // We look for any code that starts with the category code to avoid issues if format changed, 
        // but strictly counting by category_id is safer and simpler for "next in line" logic.
        $count = ActivoFijo::where('categoria_id', $request->categoria_id)->count();
        $sequence = $count + 1;

        // Generate code: CATEGORY_CODE-SEQUENCE (4 digits)
        // e.g., 1000-1-01-0001
        $nextCode = $categoria->codigo . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return response()->json(['code' => $nextCode]);
    }
}
