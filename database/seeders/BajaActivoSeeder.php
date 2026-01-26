<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BajaActivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $year = $now->format('y');

        $bajasData = [
            ['nombre_cat' => 'Computadoras de escritorio', 'desc' => 'Computadora Dell Inservible', 'valor' => 25000, 'motivo' => 'Obsolescencia'],
            ['nombre_cat' => 'Laptops', 'desc' => 'Laptop HP con pantalla rota', 'valor' => 15000, 'motivo' => 'Daño irreparable'],
            ['nombre_cat' => 'Sillas', 'desc' => 'Silla de madera quebrada', 'valor' => 2000, 'motivo' => 'Deterioro'],
            ['nombre_cat' => 'Impresoras', 'desc' => 'Impresora Epson vieja', 'valor' => 8000, 'motivo' => 'Falla técnica constante'],
            ['nombre_cat' => 'Vehículos sedán', 'desc' => 'Vehículo Toyota 2005 Chatarra', 'valor' => 45000, 'motivo' => 'Siniestro'],
        ];

        $counts = [];

        foreach ($bajasData as $item) {
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
                'estado_activo_id' => 2,
                'fecha_adquisicion' => '2018-05-10',
                'valor_adquisicion' => $item['valor'],
                'vida_util' => 5,
                'valor_residual' => 0,
                'proveedor_id' => 1,
                'fuente_id' => 1,
                'ubicacion_id' => 1,
                'responsable_id' => 1,
                'departamento_id' => 1,
                'creado_por' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('bajas_activos')->insert([
                'activo_fijo_id' => $activoId,
                'fecha_baja' => '2025-' . str_pad(rand(1, 6), 2, '0', STR_PAD_LEFT) . '-15',
                'autorizado_por' => 1,
                'creado_por' => 1,
                'motivo' => $item['motivo'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
