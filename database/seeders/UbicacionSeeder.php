<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbicacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ubicaciones = [
            ['nombre' => 'Edificio Principal - Oficina del Alcalde'],
            ['nombre' => 'Edificio Principal - Sala de Reuniones'],
            ['nombre' => 'Edificio Principal - Departamento de Finanzas'],
            ['nombre' => 'Edificio Principal - Recursos Humanos'],
            ['nombre' => 'Edificio Anexo - Servicios Municipales'],
            ['nombre' => 'Bodega Central'],
            ['nombre' => 'Taller Municipal'],
            ['nombre' => 'Parque Vehicular'],
            ['nombre' => 'Centro de Cómputo'],
            ['nombre' => 'Oficina de Catastro'],
        ];

        foreach ($ubicaciones as $ubicacion) {
            \App\Models\Ubicacion::firstOrCreate($ubicacion);
        }
    }
}
