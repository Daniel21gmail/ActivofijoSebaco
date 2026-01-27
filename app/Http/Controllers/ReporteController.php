<?php

namespace App\Http\Controllers;

use App\Models\ActivoFijo;
use App\Models\Departamento;
use App\Models\Ubicacion;
use App\Models\PersonalResponsable;
use App\Models\EstadoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActivosExport;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'inventario');
        $previewData = [];

        // Only fetch data if we are "generating results" or on initial load if desired
        // For now, let's return empty or all based on a flag, or just return default view

        if ($reportType === 'acciones') {
            $query = \App\Models\Movimiento::with([
                'activoFijo',
                'ubicacionOrigen',
                'ubicacionDestino',
                'responsableOrigen',
                'responsableDestino',
                'autorizadoPor'
            ])->orderBy('fecha_movimiento', 'desc');

            if ($request->fecha_inicio && $request->fecha_fin) {
                $query->whereBetween('fecha_movimiento', [$request->fecha_inicio, $request->fecha_fin]);
            }
            $previewData = $query->paginate(15)->withQueryString();

        } elseif ($reportType === 'depreciacion') {
            // Re-use logic from getFilteredQuery but might need extra formatting for Vue
            // For preview, we just send the objects, calculation happens in Vue or we append generic attribute
            $previewData = $this->getFilteredQuery($request)->paginate(15)->withQueryString();

        } elseif ($reportType === 'mantenimientos') {
            $query = \App\Models\Mantenimiento::with([
                'activoFijo',
                'proveedor',
                'responsable',
                'creadoPor'
            ])->orderBy('fecha_inicio', 'desc');

            if ($request->fecha_inicio && $request->fecha_fin) {
                $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin]);
            }

            if ($request->responsable_id) {
                $query->where('responsable_id', $request->responsable_id);
            }

            $previewData = $query->paginate(15)->withQueryString();

        } elseif ($reportType === 'categoria') {
            // Group by category for preview
            $categorias = \App\Models\Categoria::withCount('activosFijos')
                ->with([
                    'activosFijos' => function ($query) use ($request) {
                        if ($request->departamento_id) {
                            $query->where('departamento_id', $request->departamento_id);
                        }
                        if ($request->ubicacion_id) {
                            $query->where('ubicacion_id', $request->ubicacion_id);
                        }
                        if ($request->responsable_id) {
                            $query->where('responsable_id', $request->responsable_id);
                        }
                        if ($request->estado_activo_id) {
                            $query->where('estado_activo_id', $request->estado_activo_id);
                        }
                        if ($request->fecha_inicio && $request->fecha_fin) {
                            $query->whereBetween('fecha_adquisicion', [$request->fecha_inicio, $request->fecha_fin]);
                        }
                    }
                ])
                ->get()
                ->map(function ($categoria) {
                    $categoria->activos_count = $categoria->activosFijos->count();
                    $categoria->valor_total = $categoria->activosFijos->sum('valor_adquisicion');
                    return $categoria;
                })
                ->filter(function ($categoria) {
                    return $categoria->activos_count > 0;
                });

            $previewData = new \Illuminate\Pagination\LengthAwarePaginator(
                $categorias->forPage(1, 15),
                $categorias->count(),
                15,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );

        } else {
            // Default Inventario
            $previewData = $this->getFilteredQuery($request)->paginate(15)->withQueryString();
        }

        return Inertia::render('Reportes/Index', [
            'catalogs' => [
                'departamentos' => Departamento::select('id', 'nombre')->orderBy('nombre')->get(),
                'ubicaciones' => Ubicacion::select('id', 'nombre')->orderBy('nombre')->get(),
                'responsables' => PersonalResponsable::select('id', 'nombre_completo')->orderBy('nombre_completo')->get(),
                'estados' => EstadoActivo::select('id', 'nombre')->orderBy('nombre')->get(),
                'categorias' => \App\Models\Categoria::select('id', 'nombre')->orderBy('nombre')->get(), // Added Categories
            ],
            'filters' => $request->all(),
            'preview_data' => $previewData
        ]);
    }

    private function getFilteredQuery(Request $request)
    {
        $query = ActivoFijo::with([
            'categoria',
            'departamento',
            'ubicacion',
            'estadoActivo',
            'responsable',
            'marca',
            'modelo'
        ]);

        if ($request->departamento_id) {
            $query->where('departamento_id', $request->departamento_id);
        }

        if ($request->ubicacion_id) {
            $query->where('ubicacion_id', $request->ubicacion_id);
        }

        if ($request->responsable_id) {
            $query->where('responsable_id', $request->responsable_id);
        }

        if ($request->estado_activo_id) {
            $query->where('estado_activo_id', $request->estado_activo_id);
        }

        if ($request->fecha_inicio && $request->fecha_fin) {
            $query->whereBetween('fecha_adquisicion', [$request->fecha_inicio, $request->fecha_fin]);
        }

        return $query;
    }

    public function downloadPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'inventario');

        if ($reportType === 'acciones') {
            return $this->downloadHistorialPdf($request);
        }

        if ($reportType === 'depreciacion') {
            return $this->downloadDepreciacionPdf($request);
        }

        if ($reportType === 'categoria') {
            return $this->downloadCategoriaPdf($request);
        }

        if ($reportType === 'mantenimientos') {
            return $this->downloadMantenimientosPdf($request);
        }

        // Default: Inventario / Others fallback to standard list for now
        $activos = $this->getFilteredQuery($request)->get();

        $filters = [
            'departamento' => $request->departamento_id ? Departamento::find($request->departamento_id)?->nombre : 'Todos',
            'ubicacion' => $request->ubicacion_id ? Ubicacion::find($request->ubicacion_id)?->nombre : 'Todas',
            'responsable' => $request->responsable_id ? PersonalResponsable::find($request->responsable_id)?->nombre_completo : 'Todos',
            'estado' => $request->estado_activo_id ? EstadoActivo::find($request->estado_activo_id)?->nombre : 'Todos',
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin
        ];

        $pdf = Pdf::loadView('reports.inventario', compact('activos', 'filters'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte_inventario_' . date('Y-m-d_H-i') . '.pdf');
    }

    public function downloadExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'inventario');
        $filters = $request->all();

        if ($reportType === 'acciones') {
            return Excel::download(new \App\Exports\HistorialExport($filters), 'reporte_historial_' . date('Y-m-d_H-i') . '.xlsx');
        }

        if ($reportType === 'depreciacion') {
            return Excel::download(new \App\Exports\DepreciacionExport($filters), 'reporte_depreciacion_' . date('Y-m-d_H-i') . '.xlsx');
        }

        return Excel::download(new ActivosExport($filters), 'reporte_inventario_' . date('Y-m-d_H-i') . '.xlsx');
    }

    private function downloadDepreciacionPdf(Request $request)
    {
        $activos = $this->getFilteredQuery($request)->get();

        $filters = [
            'departamento' => $request->departamento_id ? Departamento::find($request->departamento_id)?->nombre : 'Todos',
            'ubicacion' => $request->ubicacion_id ? Ubicacion::find($request->ubicacion_id)?->nombre : 'Todas',
            'responsable' => $request->responsable_id ? PersonalResponsable::find($request->responsable_id)?->nombre_completo : 'Todos',
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin
        ];

        $pdf = Pdf::loadView('reports.depreciacion', compact('activos', 'filters'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte_depreciacion_' . date('Y-m-d_H-i') . '.pdf');
    }

    private function downloadHistorialPdf(Request $request)
    {
        $query = \App\Models\Movimiento::with([
            'activoFijo',
            'ubicacionOrigen',
            'ubicacionDestino',
            'responsableOrigen',
            'responsableDestino',
            'autorizadoPor'
        ])->orderBy('fecha_movimiento', 'desc');

        if ($request->fecha_inicio && $request->fecha_fin) {
            $query->whereBetween('fecha_movimiento', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $movimientos = $query->get();

        $filters = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin
        ];

        $pdf = Pdf::loadView('reports.historial', compact('movimientos', 'filters'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('reporte_historial_' . date('Y-m-d_H-i') . '.pdf');
    }

    private function downloadCategoriaPdf(Request $request)
    {
        // Group assets by category with count and total value
        $categorias = \App\Models\Categoria::withCount('activosFijos')
            ->with([
                'activosFijos' => function ($query) use ($request) {
                    // Apply filters if needed
                    if ($request->departamento_id) {
                        $query->where('departamento_id', $request->departamento_id);
                    }
                    if ($request->ubicacion_id) {
                        $query->where('ubicacion_id', $request->ubicacion_id);
                    }
                    if ($request->responsable_id) {
                        $query->where('responsable_id', $request->responsable_id);
                    }
                    if ($request->estado_activo_id) {
                        $query->where('estado_activo_id', $request->estado_activo_id);
                    }
                    if ($request->fecha_inicio && $request->fecha_fin) {
                        $query->whereBetween('fecha_adquisicion', [$request->fecha_inicio, $request->fecha_fin]);
                    }
                }
            ])
            ->get()
            ->map(function ($categoria) {
                $categoria->activos_count = $categoria->activosFijos->count();
                $categoria->valor_total = $categoria->activosFijos->sum('valor_adquisicion');
                return $categoria;
            })
            ->filter(function ($categoria) {
                return $categoria->activos_count > 0; // Only show categories with assets
            });

        $pdf = Pdf::loadView('reports.categoria', compact('categorias'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('reporte_categoria_' . date('Y-m-d_H-i') . '.pdf');
    }
    private function downloadMantenimientosPdf(Request $request)
    {
        $query = \App\Models\Mantenimiento::with([
            'activoFijo',
            'proveedor',
            'responsable',
            'creadoPor'
        ])->orderBy('fecha_inicio', 'desc');

        if ($request->fecha_inicio && $request->fecha_fin) {
            $query->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin]);
        }

        if ($request->responsable_id) {
            $query->where('responsable_id', $request->responsable_id);
        }

        $mantenimientos = $query->get();

        $filters = [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'responsable' => $request->responsable_id ? PersonalResponsable::find($request->responsable_id)?->nombre_completo : 'Todos'
        ];

        $pdf = Pdf::loadView('reports.mantenimientos', compact('mantenimientos', 'filters'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('reporte_mantenimientos_' . date('Y-m-d_H-i') . '.pdf');
    }
}
