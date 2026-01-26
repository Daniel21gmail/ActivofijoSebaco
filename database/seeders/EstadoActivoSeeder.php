<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoActivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            'PROPIO',
            'ARRENDADO',
            'EN COMODATO',
            'DONADO',
            'EN LITIGIO',
            'CESIÓN DE DERECHOS',
            'EN REPARACIÓN',
            'FUERA DE SERVICIO',
            'EXTRAVIADO',
            'PENDIENTE DE BAJA',
        ];

        foreach ($estados as $estado) {
            \App\Models\EstadoActivo::firstOrCreate(['nombre' => $estado]);
        }
    }
}
