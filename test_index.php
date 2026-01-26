<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

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

try {
    echo "Fetching items...\n";
    $items = ActivoFijo::with([
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
    ])->get();

    echo "Count: " . $items->count() . "\n";
    if ($items->count() > 0) {
        $first = $items->first();
        echo "First Item: " . $first->codigo . " - " . $first->descripcion . "\n";
        echo "Responsable: " . ($first->responsable->nombre_completo ?? 'NULL') . "\n";
    }

    echo "Fetching catalogs...\n";
    $catalogs = [
        'marcas' => Marca::all()->count(),
        'modelos' => Modelo::all()->count(),
        'colores' => Color::all()->count(),
        'fuentes' => Fuente::all()->count(),
        'proveedores' => Proveedor::all()->count(),
        'responsables' => PersonalResponsable::all()->count(),
        'estados' => EstadoActivo::all()->count(),
        'ubicaciones' => Ubicacion::all()->count(),
        'categorias' => Categoria::all()->count(),
        'departamentos' => Departamento::all()->count(),
    ];

    print_r($catalogs);
    echo "SUCCESS\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
