<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivoFijoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $year = $now->format('y');

        $data = [
            ['nombre_cat' => 'Computadoras de escritorio', 'desc' => 'Computadora de Escritorio Dell', 'serie' => 'DELL-2025-001', 'valor' => 45000, 'ubi' => 9, 'resp' => 3, 'dep' => 9],
            ['nombre_cat' => 'Laptops', 'desc' => 'Laptop HP ProBook', 'serie' => 'HP-2025-002', 'valor' => 38000, 'ubi' => 3, 'resp' => 2, 'dep' => 2],
            ['nombre_cat' => 'Impresoras', 'desc' => 'Monitor LG 27 pulgadas', 'serie' => 'LG-2025-003', 'valor' => 12000, 'ubi' => 9, 'resp' => 3, 'dep' => 9],
            ['nombre_cat' => 'Escritorios', 'desc' => 'Escritorio Ejecutivo de Madera', 'serie' => null, 'valor' => 18000, 'ubi' => 1, 'resp' => 1, 'dep' => 1],
            ['nombre_cat' => 'Sillas', 'desc' => 'Silla Ergonómica de Oficina', 'serie' => null, 'valor' => 8500, 'ubi' => 1, 'resp' => 1, 'dep' => 1],
            ['nombre_cat' => 'Laptops', 'desc' => 'Aire Acondicionado 18000 BTU', 'serie' => 'SAM-AC-2025-006', 'valor' => 25000, 'ubi' => 2, 'resp' => 5, 'dep' => 1],
            ['nombre_cat' => 'Impresoras', 'desc' => 'Impresora Multifuncional HP', 'serie' => 'HP-IMP-2025-007', 'valor' => 15000, 'ubi' => 4, 'resp' => 4, 'dep' => 3, 'estado' => 3],
            ['nombre_cat' => 'Laptops', 'desc' => 'Tablet Samsung Galaxy Tab A8', 'serie' => 'SAM-TAB-2025-008', 'valor' => 18000, 'ubi' => 5, 'resp' => 6, 'dep' => 4],
            ['nombre_cat' => 'Archivadores', 'desc' => 'Archivador Metálico 4 Gavetas', 'serie' => null, 'valor' => 12000, 'ubi' => 4, 'resp' => 4, 'dep' => 3],
            ['nombre_cat' => 'Proyectores', 'desc' => 'Proyector Multimedia', 'serie' => 'LEN-PROY-2025-010', 'valor' => 32000, 'ubi' => 2, 'resp' => 5, 'dep' => 1, 'estado' => 3],
        ];

        $counts = [];

        foreach ($data as $item) {
            $categoria = DB::table('categorias')->where('nombre', $item['nombre_cat'])->first();
            if (!$categoria)
                continue;

            $currentCount = DB::table('activos_fijos')
                ->where('categoria_id', $categoria->id)
                ->where('codigo', 'like', "{$year}-{$categoria->codigo}-%")
                ->count();

            $codigo = "{$year}-{$categoria->codigo}-" . str_pad($currentCount + 1, 2, '0', STR_PAD_LEFT);

            DB::table('activos_fijos')->insert([
                'codigo' => $codigo,
                'descripcion' => $item['desc'],
                'categoria_id' => $categoria->id,
                'marca_id' => null, // Simplified for brevity in this seeder block
                'modelo_id' => null,
                'serie' => $item['serie'],
                'estado_activo_id' => $item['estado'] ?? 1,
                'fecha_adquisicion' => '2025-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'valor_adquisicion' => $item['valor'],
                'vida_util' => 5,
                'valor_residual' => $item['valor'] * 0.1,
                'proveedor_id' => 1,
                'fuente_id' => 1,
                'ubicacion_id' => $item['ubi'],
                'responsable_id' => $item['resp'],
                'departamento_id' => $item['dep'],
                'creado_por' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
