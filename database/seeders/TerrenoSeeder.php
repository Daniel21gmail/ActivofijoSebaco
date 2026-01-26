<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TerrenoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $year = $now->format('y');

        $terrenosData = [
            ['nombre_cat' => 'Terrenos urbanos', 'desc' => 'Terreno Edificio Municipal', 'area' => 1500, 'uso' => 'Institucional'],
            ['nombre_cat' => 'Terrenos urbanos', 'desc' => 'Terreno Parque Central', 'area' => 5000, 'uso' => 'Público'],
            ['nombre_cat' => 'Terrenos rurales', 'desc' => 'Terreno Vertedero Municipal', 'area' => 20000, 'uso' => 'Servicios'],
            ['nombre_cat' => 'Áreas verdes públicas', 'desc' => 'Parque Infantil San José', 'area' => 800, 'uso' => 'Recreativo'],
            ['nombre_cat' => 'Terrenos urbanos', 'desc' => 'Terreno Mercado Municipal', 'area' => 2500, 'uso' => 'Comercial'],
            ['nombre_cat' => 'Terrenos urbanos', 'desc' => 'Lote para Futuro Centro de Salud', 'area' => 1200, 'uso' => 'Salud'],
            ['nombre_cat' => 'Terrenos rurales', 'desc' => 'Terreno Cementerio Nuevo', 'area' => 15000, 'uso' => 'Servicios', 'estado' => 3],
            ['nombre_cat' => 'Áreas verdes públicas', 'desc' => 'Cancha Multiuso Sébaco Viejo', 'area' => 600, 'uso' => 'Deportivo'],
            ['nombre_cat' => 'Terrenos urbanos', 'desc' => 'Terreno Planta de Tratamiento', 'area' => 3000, 'uso' => 'Servicios'],
            ['nombre_cat' => 'Terrenos urbanos', 'desc' => 'Lote Alcaldía Auxiliar', 'area' => 450, 'uso' => 'Administrativo', 'estado' => 3],
        ];

        $counts = [];

        foreach ($terrenosData as $item) {
            $categoria = DB::table('categorias')->where('nombre', $item['nombre_cat'])->first();
            if (!$categoria)
                continue;

            $currentCount = DB::table('activos_fijos')
                ->where('categoria_id', $categoria->id)
                ->where('codigo', 'like', "{$year}-{$categoria->codigo}-%")
                ->count();

            $codigo = "{$year}-{$categoria->codigo}-" . str_pad($currentCount + 1, 2, '0', STR_PAD_LEFT);

            $activoId = DB::table('activos_fijos')->insertGetId([
                'codigo' => $codigo,
                'descripcion' => $item['desc'],
                'categoria_id' => $categoria->id,
                'marca_id' => null,
                'modelo_id' => null,
                'serie' => null,
                'estado_activo_id' => $item['estado'] ?? 1,
                'fecha_adquisicion' => '2025-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'valor_adquisicion' => rand(500000, 3000000),
                'vida_util' => 50,
                'valor_residual' => 0,
                'proveedor_id' => 10,
                'fuente_id' => 1,
                'ubicacion_id' => 1,
                'responsable_id' => 1,
                'departamento_id' => 1,
                'creado_por' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('terrenos')->insert([
                'activo_fijo_id' => $activoId,
                'area_m2' => $item['area'],
                'folio_real' => 'FR-' . rand(10000, 99999),
                'catastro' => 'CAT-' . rand(10000, 99999),
                'uso' => $item['uso'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
