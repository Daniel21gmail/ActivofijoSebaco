<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\EstadoActivoController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FuenteController;
use App\Http\Controllers\PersonalResponsableController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivoFijoController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\BajaActivoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\TerrenoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catalogs
    Route::resource('departamentos', DepartamentoController::class);
    Route::resource('ubicaciones', UbicacionController::class)->parameters(['ubicaciones' => 'ubicacion']);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor']);
    Route::resource('marcas', MarcaController::class);
    Route::resource('modelos', ModeloController::class);
    Route::resource('estados-activos', EstadoActivoController::class)->parameters(['estados-activos' => 'estadoActivo']);
    Route::resource('colores', ColorController::class)->parameters(['colores' => 'color']);
    Route::resource('fuentes', FuenteController::class);
    Route::resource('responsables', PersonalResponsableController::class)->parameters(['responsables' => 'personalResponsable']);
    Route::resource('cargos', CargoController::class);
    Route::resource('tecnicos', TecnicoController::class);
    Route::resource('roles', RoleController::class)->names('roles');
    Route::resource('usuarios', UserController::class)->names('usuarios')->parameters(['usuarios' => 'user']);
    Route::get('/activos-fijos/next-code', [ActivoFijoController::class, 'getNextCode'])->name('activos-fijos.next-code');
    Route::resource('activos-fijos', ActivoFijoController::class)->parameters(['activos-fijos' => 'activoFijo']);
    Route::resource('vehiculos', VehiculoController::class);
    Route::resource('terrenos', TerrenoController::class);
    Route::post('movimientos', [MovimientoController::class, 'store'])->name('movimientos.store');
    Route::post('mantenimientos', [MantenimientoController::class, 'store'])->name('mantenimientos.store');
    Route::get('/mantenimientos/{id}/imprimir', [MantenimientoController::class, 'print'])->name('mantenimientos.print');
    Route::post('bajas-activos', [BajaActivoController::class, 'store'])->name('bajas-activos.store');
    Route::get('/activos-fijos/{activoFijo}/print', [ActivoFijoController::class, 'print'])->name('activos-fijos.print');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/pdf', [ReporteController::class, 'downloadPdf'])->name('reportes.pdf');
    Route::get('/reportes/excel', [ReporteController::class, 'downloadExcel'])->name('reportes.excel');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__ . '/auth.php';
