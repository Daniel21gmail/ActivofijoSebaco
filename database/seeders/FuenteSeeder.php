<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fuentes = [
            ['nombre' => 'Recursos Propios'],
            ['nombre' => 'Donación'],
            ['nombre' => 'Préstamo'],
            ['nombre' => 'Transferencia del Gobierno Central'],
            ['nombre' => 'Fondos Externos'],
            ['nombre' => 'Cooperación Internacional'],
            ['nombre' => 'Proyecto de Desarrollo'],
            ['nombre' => 'Fondo de Inversión Municipal'],
            ['nombre' => 'Recursos del FISE'],
            ['nombre' => 'Otros'],
        ];

        foreach ($fuentes as $fuente) {
            \App\Models\Fuente::firstOrCreate($fuente);
        }
    }
}
