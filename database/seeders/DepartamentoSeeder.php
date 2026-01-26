<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            ['nombre' => 'Administración'],
            ['nombre' => 'Finanzas'],
            ['nombre' => 'Recursos Humanos'],
            ['nombre' => 'Servicios Municipales'],
            ['nombre' => 'Obras Públicas'],
            ['nombre' => 'Catastro'],
            ['nombre' => 'Medio Ambiente'],
            ['nombre' => 'Desarrollo Social'],
            ['nombre' => 'Tecnología de la Información'],
            ['nombre' => 'Auditoría Interna'],
        ];

        foreach ($departamentos as $departamento) {
            \App\Models\Departamento::firstOrCreate($departamento);
        }
    }
}
