<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargos = [
            'Alcalde Municipal',
            'Secretario Municipal',
            'Director Administrativo',
            'Jefe de Adquisiciones',
            'Responsable de Activos Fijos',
            'Encargado de Almacén',
            'Secretaria',
            'Contador',
            'Técnico de Informática',
            'Conductor',
            'Operador de Maquinaria',
            'Asistente Administrativo',
        ];

        foreach ($cargos as $cargo) {
            Cargo::updateOrCreate(['nombre' => $cargo]);
        }
    }
}
