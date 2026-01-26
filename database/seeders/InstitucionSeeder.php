<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstitucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Departamentos
        $departamentos = [
            'ADMINISTRACIÓN CENTRAL',
            'PATRIMONIO MUNICIPAL',
            'OBRAS PÚBLICAS',
            'SERVICIOS MUNICIPALES',
            'CATASTRO MUNICIPAL',
        ];

        foreach ($departamentos as $nombre) {
            \App\Models\Departamento::firstOrCreate(['nombre' => $nombre]);
        }

        // Ubicaciones
        $ubicaciones = [
            ['nombre' => 'TERRITORIO MUNICIPAL', 'descripcion' => 'Ubicación general para bienes inmuebles sin edificio específico'],
            ['nombre' => 'ZONA URBANA', 'descripcion' => 'Bienes ubicados dentro del casco urbano'],
            ['nombre' => 'ZONA RURAL', 'descripcion' => 'Bienes ubicados en comarcas o zonas rurales'],
            ['nombre' => 'PLANTEL MUNICIPAL', 'descripcion' => 'Maquinaria y equipo pesado'],
            ['nombre' => 'SIN ASIGNACIÓN ESPECÍFICA', 'descripcion' => 'Bienes pendientes de ubicación definitiva'],
        ];

        foreach ($ubicaciones as $ubicacion) {
            \App\Models\Ubicacion::firstOrCreate(
                ['nombre' => $ubicacion['nombre']],
                ['descripcion' => $ubicacion['descripcion']]
            );
        }
    }
}
