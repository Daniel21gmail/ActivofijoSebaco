<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalResponsableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personal = [
            ['nombre_completo' => 'Juan Carlos Pérez', 'cargo_id' => 1, 'telefono' => '8888-1234'],
            ['nombre_completo' => 'María Elena Rodríguez', 'cargo_id' => 2, 'telefono' => '8888-2345'],
            ['nombre_completo' => 'Roberto Martínez', 'cargo_id' => 3, 'telefono' => '8888-3456'],
            ['nombre_completo' => 'Ana Patricia López', 'cargo_id' => 4, 'telefono' => '8888-4567'],
            ['nombre_completo' => 'Carlos Alberto Gómez', 'cargo_id' => 5, 'telefono' => '8888-5678'],
            ['nombre_completo' => 'Silvia Hernández', 'cargo_id' => 1, 'telefono' => '8888-6789'],
            ['nombre_completo' => 'Miguel Ángel Flores', 'cargo_id' => 2, 'telefono' => '8888-7890'],
            ['nombre_completo' => 'Patricia Morales', 'cargo_id' => 3, 'telefono' => '8888-8901'],
            ['nombre_completo' => 'José Luis Ramírez', 'cargo_id' => 4, 'telefono' => '8888-9012'],
            ['nombre_completo' => 'Laura Beatriz Castro', 'cargo_id' => 5, 'telefono' => '8888-0123'],
        ];

        DB::table('personal_responsables')->insert($personal);
    }
}
