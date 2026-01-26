<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarioDetalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $inventarioIds = DB::table('inventarios')->pluck('id')->toArray();
        $activoIds = DB::table('activos_fijos')->pluck('id')->toArray();
        if (empty($inventarioIds) || empty($activoIds))
            return;

        $detalles = [];
        foreach ($inventarioIds as $invId) {
            // Seleccionar 5 activos por cada inventario
            $randomActivos = array_rand(array_flip($activoIds), 5);

            foreach ($randomActivos as $activoId) {
                $detalles[] = [
                    'inventario_id' => $invId,
                    'activo_fijo_id' => $activoId,
                    'ubicacion_origen_id' => rand(1, 10),
                    'ubicacion_verificada_id' => rand(1, 10),
                    'responsable_verificado_id' => rand(1, 10),
                    'estado_fisico_id' => rand(1, 3),
                    'verificado' => rand(0, 1),
                    'verificado_por' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('inventario_detalles')->insert($detalles);
    }
}
