<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MantenimientoSeeder extends Seeder
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

        $mantenimientos = [];
        $tipos = ['Preventivo', 'Correctivo', 'Revisión'];

        for ($i = 0; $i < 10; $i++) {
            $mantenimientos[] = [
                'activo_fijo_id' => $activoIds[array_rand($activoIds)],
                'fecha_inicio' => $now->subDays(rand(1, 60))->toDateString(),
                'costo' => rand(500, 5000),
                'proveedor_id' => rand(1, 10),
                'responsable_id' => rand(1, 10),
                'creado_por' => 1,
                'tipo' => $tipos[array_rand($tipos)],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('mantenimientos')->insert($mantenimientos);
    }
}
