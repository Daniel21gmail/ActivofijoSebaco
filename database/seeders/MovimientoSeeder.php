<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MovimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Obtener IDs de activos fijos disponibles
        $activoIds = DB::table('activos_fijos')->pluck('id')->toArray();
        if (empty($activoIds))
            return;

        $movimientos = [];
        for ($i = 0; $i < 10; $i++) {
            $movimientos[] = [
                'activo_fijo_id' => $activoIds[array_rand($activoIds)],
                'fecha_movimiento' => $now->subDays(rand(1, 30))->toDateTimeString(),
                'ubicacion_origen_id' => rand(1, 5),
                'ubicacion_destino_id' => rand(6, 10),
                'responsable_origen_id' => rand(1, 5),
                'responsable_destino_id' => rand(6, 10),
                'autorizado_por' => 1,
                'creado_por' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('movimientos')->insert($movimientos);
    }
}
