<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TecnicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tecnicos = [
            ['nombre_completo' => 'Pedro Antonio Sánchez', 'telefono' => '8777-1234'],
            ['nombre_completo' => 'Luis Fernando García', 'telefono' => '8777-2345'],
            ['nombre_completo' => 'Jorge Alberto Méndez', 'telefono' => '8777-3456'],
            ['nombre_completo' => 'Ricardo José Ortiz', 'telefono' => '8777-4567'],
            ['nombre_completo' => 'Fernando Javier Cruz', 'telefono' => '8777-5678'],
            ['nombre_completo' => 'Héctor Manuel Díaz', 'telefono' => '8777-6789'],
            ['nombre_completo' => 'Oscar René Vega', 'telefono' => '8777-7890'],
            ['nombre_completo' => 'Sergio Antonio Ruiz', 'telefono' => '8777-8901'],
            ['nombre_completo' => 'Daniel Eduardo Torres', 'telefono' => '8777-9012'],
            ['nombre_completo' => 'Alfredo José Vargas', 'telefono' => '8777-0123'],
        ];

        DB::table('tecnicos')->insert($tecnicos);
    }
}
