<?php

namespace App\Http\Controllers;

use App\Models\ActivoFijo;
use App\Models\Vehiculo;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VehiculoController extends Controller
{
    public function index()
    {
        return Inertia::render('Vehiculos/Index', [
            'items' => Vehiculo::with([
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
        return Inertia::render('Vehiculos/Create', [
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
        Log::info('Attempting to store vehicle', $request->all());
        $validated = $request->validate([
            // Activo Fijo
            'descripcion' => 'required|string|max:255',
            'serie' => 'nullable|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'vida_util' => 'required|integer|min:0',
            'valor_residual' => 'required|numeric|min:0',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'color_id' => 'required|exists:colores,id',
            'fuente_id' => 'required|exists:fuentes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'responsable_id' => 'required|exists:personal_responsables,id',
            'estado_activo_id' => 'required|exists:estados_activos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'imagen' => 'nullable|image|max:2048',
            // Vehiculo
            'placa' => 'required|string|max:15|unique:vehiculos,placa',
            'chasis' => 'required|string|max:50',
            'motor' => 'required|string|max:50',
            'numero_circulacion' => 'required|string|max:30',
        ]);

        DB::beginTransaction();
        try {
            $activoData = [
                'descripcion' => $validated['descripcion'],
                'serie' => $validated['serie'] ?? null,
                'categoria_id' => $validated['categoria_id'],
                'departamento_id' => $validated['departamento_id'],
                'fecha_adquisicion' => $validated['fecha_adquisicion'],
                'valor_adquisicion' => $validated['valor_adquisicion'],
                'vida_util' => $validated['vida_util'],
                'valor_residual' => $validated['valor_residual'],
                'marca_id' => $validated['marca_id'],
                'modelo_id' => $validated['modelo_id'],
                'color_id' => $validated['color_id'],
                'fuente_id' => $validated['fuente_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'responsable_id' => $validated['responsable_id'],
                'estado_activo_id' => $validated['estado_activo_id'],
                'ubicacion_id' => $validated['ubicacion_id'],
                'creado_por' => Auth::id()
            ];

            if ($request->hasFile('imagen')) {
                $activoData['imagen'] = $request->file('imagen')->store('activos', 'public');
            }

            // Generate automatic code
            $categoria = Categoria::findOrFail($validated['categoria_id']);
            $year = date('y');
            $seq = ActivoFijo::where('categoria_id', $validated['categoria_id'])
                ->whereYear('created_at', date('Y'))
                ->count() + 1;

            $activoData['codigo'] = "{$year}-{$categoria->codigo}-" . str_pad($seq, 2, '0', STR_PAD_LEFT);

            $activoFijo = ActivoFijo::create($activoData);

            Vehiculo::create([
                'activo_fijo_id' => $activoFijo->id,
                'placa' => strtoupper($validated['placa']),
                'chasis' => strtoupper($validated['chasis']),
                'motor' => strtoupper($validated['motor']),
                'numero_circulacion' => strtoupper($validated['numero_circulacion']),
            ]);

            DB::commit();
            Log::info('Vehicle registered successfully', ['vehiculo_id' => $activoFijo->id]);
            return redirect()->route('vehiculos.index')->with('message', 'Vehículo registrado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registering vehicle', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Error al registrar el vehículo: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Vehiculo $vehiculo)
    {
        return Inertia::render('Vehiculos/Edit', [
            'vehiculo' => $vehiculo->load('activoFijo'),
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

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $activoFijo = $vehiculo->activoFijo;

        $validated = $request->validate([
            // Activo Fijo
            'descripcion' => 'required|string|max:255',
            'serie' => 'nullable|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_adquisicion' => 'required|date',
            'valor_adquisicion' => 'required|numeric|min:0',
            'vida_util' => 'required|integer|min:0',
            'valor_residual' => 'required|numeric|min:0',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'color_id' => 'required|exists:colores,id',
            'fuente_id' => 'required|exists:fuentes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'responsable_id' => 'required|exists:personal_responsables,id',
            'estado_activo_id' => 'required|exists:estados_activos,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'imagen' => 'nullable|image|max:2048',
            // Vehiculo
            'placa' => 'required|string|max:15|unique:vehiculos,placa,' . $vehiculo->id,
            'chasis' => 'required|string|max:50',
            'motor' => 'required|string|max:50',
            'numero_circulacion' => 'required|string|max:30',
        ]);

        DB::beginTransaction();
        try {
            $activoData = [
                'descripcion' => $validated['descripcion'],
                'serie' => $validated['serie'] ?? null,
                'categoria_id' => $validated['categoria_id'],
                'departamento_id' => $validated['departamento_id'],
                'fecha_adquisicion' => $validated['fecha_adquisicion'],
                'valor_adquisicion' => $validated['valor_adquisicion'],
                'vida_util' => $validated['vida_util'],
                'valor_residual' => $validated['valor_residual'],
                'marca_id' => $validated['marca_id'],
                'modelo_id' => $validated['modelo_id'],
                'color_id' => $validated['color_id'],
                'fuente_id' => $validated['fuente_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'responsable_id' => $validated['responsable_id'],
                'estado_activo_id' => $validated['estado_activo_id'],
                'ubicacion_id' => $validated['ubicacion_id'],
                'modificado_por' => Auth::id()
            ];

            if ($request->hasFile('imagen')) {
                if ($activoFijo->imagen) {
                    Storage::disk('public')->delete($activoFijo->imagen);
                }
                $activoData['imagen'] = $request->file('imagen')->store('activos', 'public');
            }

            $activoFijo->update($activoData);

            $vehiculo->update([
                'placa' => strtoupper($validated['placa']),
                'chasis' => strtoupper($validated['chasis']),
                'motor' => strtoupper($validated['motor']),
                'numero_circulacion' => strtoupper($validated['numero_circulacion']),
            ]);

            DB::commit();
            return redirect()->route('vehiculos.index')->with('message', 'Vehículo actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el vehículo: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Vehiculo $vehiculo)
    {
        DB::beginTransaction();
        try {
            $activoFijo = $vehiculo->activoFijo;

            if ($activoFijo->imagen) {
                Storage::disk('public')->delete($activoFijo->imagen);
            }

            $vehiculo->delete();
            $activoFijo->delete();

            DB::commit();
            return redirect()->back()->with('message', 'Vehículo eliminado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el vehículo: ' . $e->getMessage());
        }
    }
}
