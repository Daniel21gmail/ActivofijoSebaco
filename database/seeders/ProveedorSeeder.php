<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            ['nombre' => 'Distribuidora La Moderna', 'telefono' => '2772-3456', 'direccion' => 'Managua, Nicaragua'],
            ['nombre' => 'Importadora del Norte', 'telefono' => '2772-4567', 'direccion' => 'Estelí, Nicaragua'],
            ['nombre' => 'Comercial Sebaco', 'telefono' => '2775-2345', 'direccion' => 'Sebaco, Matagalpa'],
            ['nombre' => 'Tecnología y Equipos S.A.', 'telefono' => '2270-1234', 'direccion' => 'Managua, Nicaragua'],
            ['nombre' => 'Automotriz Central', 'telefono' => '2772-5678', 'direccion' => 'Matagalpa, Nicaragua'],
            ['nombre' => 'Muebles y Equipos de Oficina', 'telefono' => '2270-2345', 'direccion' => 'Managua, Nicaragua'],
            ['nombre' => 'Suministros Municipales', 'telefono' => '2775-3456', 'direccion' => 'Sebaco, Matagalpa'],
            ['nombre' => 'Constructora del Valle', 'telefono' => '2772-6789', 'direccion' => 'Matagalpa, Nicaragua'],
            ['nombre' => 'Importaciones Técnicas', 'telefono' => '2270-3456', 'direccion' => 'Managua, Nicaragua'],
            ['nombre' => 'Distribuidora Regional', 'telefono' => '2772-7890', 'direccion' => 'Jinotega, Nicaragua'],
        ];

        DB::table('proveedores')->insert($proveedores);
    }
}
