<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $year = $now->format('y');

        $vehiculos = [
            ['nombre_cat' => 'Camionetas pickup', 'desc' => 'Camioneta Toyota Hilux 4x4', 'valor' => 450000, 'placa' => 'M 123-456'],
            ['nombre_cat' => 'Vehículos sedán', 'desc' => 'Toyota Corolla Municipal', 'valor' => 250000, 'placa' => 'M 789-012'],
            ['nombre_cat' => 'Motocicletas', 'desc' => 'Moto Suzuki DR650 Transito', 'valor' => 85000, 'placa' => 'M 111-222'],
            ['nombre_cat' => 'Camiones de recolección', 'desc' => 'Camión Basurero Mercedes-Benz', 'valor' => 1250000, 'placa' => 'M 333-444'],
            ['nombre_cat' => 'Tractores viales', 'desc' => 'Tractor Caterpillar Bulldozer', 'valor' => 2500000, 'placa' => 'N/A'],
            ['nombre_cat' => 'Camionetas pickup', 'desc' => 'Camioneta Mitsubishi L200', 'valor' => 420000, 'placa' => 'M 555-666'],
            ['nombre_cat' => 'Vehículos sedán', 'desc' => 'Hyundai Accent Administración', 'valor' => 180000, 'placa' => 'M 777-888', 'estado' => 3],
            ['nombre_cat' => 'Motocicletas', 'desc' => 'Moto Yamaha AG200 Mensajería', 'valor' => 45000, 'placa' => 'M 999-000'],
            ['nombre_cat' => 'Camiones de recolección', 'desc' => 'Camión Basurero Freightliner', 'valor' => 1450000, 'placa' => 'M 222-333'],
            ['nombre_cat' => 'Camionetas pickup', 'desc' => 'Camioneta Isuzu D-Max', 'valor' => 480000, 'placa' => 'M 444-555', 'estado' => 3],
        ];

        $counts = [];

        foreach ($vehiculos as $item) {
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
                'color_id' => rand(1, 10), // Move to activos_fijos and use 1-10 range (standard)
                'serie' => null,
                'estado_activo_id' => $item['estado'] ?? 1,
                'fecha_adquisicion' => '2025-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'valor_adquisicion' => $item['valor'],
                'vida_util' => 5,
                'valor_residual' => $item['valor'] * 0.1,
                'proveedor_id' => 5,
                'fuente_id' => 1,
                'ubicacion_id' => 8,
                'responsable_id' => 1,
                'departamento_id' => 1,
                'creado_por' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('vehiculos')->insert([
                'activo_fijo_id' => $activoId,
                'placa' => $item['placa'],
                'motor' => 'MOT-' . rand(1000, 9999), // Correct column name
                'chasis' => 'CHA-' . rand(1000, 9999), // Correct column name
                'numero_circulacion' => 'CIRC-' . rand(1000, 9999),
                'anio' => 2024,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
