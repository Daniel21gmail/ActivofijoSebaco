<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $inventarios = [];
        $estados = ['Borrador', 'Activo', 'Cerrado'];

        for ($i = 0; $i < 10; $i++) {
            $inventarios[] = [
                'fecha_inicio' => $now->subMonths($i)->toDateString(),
                'responsable_id' => rand(1, 10),
                'nombre' => 'Inventario General ' . (2024 - $i),
                'estado' => $estados[array_rand($estados)],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('inventarios')->insert($inventarios);
    }
}
