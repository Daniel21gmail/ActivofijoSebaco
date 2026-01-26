<?php

namespace App\Http\Controllers;

use App\Models\ActivoFijo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Fetch all assets to calculate PHP-side accessors
        // optimization: select only necessary columns if possible, but accessors might need more.
        // For now, simpler to get all.
        $activos = ActivoFijo::all();

        // 2. High Level Stats
        $totalActivos = $activos->count();
        $valorTotal = $activos->sum('valor_adquisicion');

        // This relies on the ActivoFijo model 'depreciacion_acumulada' accessor
        $depreciacionAcumulada = $activos->sum('depreciacion_acumulada');

        $valorNetoActual = $valorTotal - $depreciacionAcumulada;

        // 3. Charts Data

        // Categorias
        $activosPorCategoria = DB::table('activos_fijos')
            ->leftJoin('categorias', 'activos_fijos.categoria_id', '=', 'categorias.id')
            ->select(DB::raw('COALESCE(categorias.nombre, "SIN CATEGORÍA") as nombre'), DB::raw('count(*) as total'))
            ->groupBy('categorias.nombre')
            ->get();

        // Estados
        $estadoActivos = DB::table('activos_fijos')
            ->leftJoin('estados_activos', 'activos_fijos.estado_activo_id', '=', 'estados_activos.id')
            ->select(DB::raw('COALESCE(estados_activos.nombre, "SIN ESTADO") as nombre'), DB::raw('count(*) as total'))
            ->groupBy('estados_activos.nombre')
            ->get();

        // Adquisiciones Recientes (Last 12 months)
        // Group by YYYY-MM
        $adquisiciones = DB::table('activos_fijos')
            ->select(DB::raw("DATE_FORMAT(fecha_adquisicion, '%Y-%m') as mes"), DB::raw('sum(valor_adquisicion) as total'))
            ->where('fecha_adquisicion', '>=', Carbon::now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_activos' => $totalActivos,
                'valor_total' => $valorTotal,
                'depreciacion_acumulada' => $depreciacionAcumulada,
                'valor_neto' => $valorNetoActual
            ],
            'chart_data' => [
                'categorias' => $activosPorCategoria,
                'estados' => $estadoActivos,
                'adquisiciones' => $adquisiciones
            ]
        ]);
    }
}
