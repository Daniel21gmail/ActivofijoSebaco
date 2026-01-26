<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colores = [
            ['nombre' => 'Blanco'],
            ['nombre' => 'Negro'],
            ['nombre' => 'Gris'],
            ['nombre' => 'Plateado'],
            ['nombre' => 'Azul'],
            ['nombre' => 'Rojo'],
            ['nombre' => 'Verde'],
            ['nombre' => 'Amarillo'],
            ['nombre' => 'Café'],
            ['nombre' => 'Beige'],
        ];

        foreach ($colores as $color) {
            \App\Models\Color::firstOrCreate($color);
        }
    }
}
