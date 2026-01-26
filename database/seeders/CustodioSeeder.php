<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustodioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure institutional cargo exists
        $cargo = \App\Models\Cargo::firstOrCreate(
            ['nombre' => 'INSTITUCIONAL']
        );

        // Create Alcaldia Municipal as responsible
        \App\Models\PersonalResponsable::firstOrCreate(
            ['nombre_completo' => 'ALCALDIA MUNICIPAL'],
            [
                'cargo_id' => $cargo->id,
                'telefono' => 'PBX-0000',
            ]
        );
    }
}
