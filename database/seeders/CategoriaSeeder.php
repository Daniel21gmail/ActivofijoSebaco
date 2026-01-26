<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['codigo' => '1000-1-01', 'subcategoria' => 'Terrenos', 'clase' => '1', 'nombre' => 'Terrenos urbanos'],
            ['codigo' => '1000-1-02', 'subcategoria' => 'Terrenos', 'clase' => '2', 'nombre' => 'Terrenos rurales'],
            ['codigo' => '1000-1-03', 'subcategoria' => 'Terrenos', 'clase' => '3', 'nombre' => 'Áreas verdes públicas'],
            ['codigo' => '1100-1-01', 'subcategoria' => 'Edificaciones e Infraestructura', 'clase' => '1', 'nombre' => 'Edificio municipal'],
            ['codigo' => '1100-1-02', 'subcategoria' => 'Edificaciones e Infraestructura', 'clase' => '2', 'nombre' => 'Mercado municipal'],
            ['codigo' => '1100-1-03', 'subcategoria' => 'Edificaciones e Infraestructura', 'clase' => '3', 'nombre' => 'Centro de salud municipal'],
            ['codigo' => '1100-1-04', 'subcategoria' => 'Edificaciones e Infraestructura', 'clase' => '4', 'nombre' => 'Escuelas municipales'],
            ['codigo' => '1100-1-05', 'subcategoria' => 'Edificaciones e Infraestructura', 'clase' => '5', 'nombre' => 'Puentes y alcantarillas'],
            ['codigo' => '1100-1-06', 'subcategoria' => 'Edificaciones e Infraestructura', 'clase' => '6', 'nombre' => 'Calles y pavimentación'],
            ['codigo' => '1200-1-01', 'subcategoria' => 'Mobiliario y Equipos de Oficina', 'clase' => '1', 'nombre' => 'Escritorios'],
            ['codigo' => '1200-1-02', 'subcategoria' => 'Mobiliario y Equipos de Oficina', 'clase' => '2', 'nombre' => 'Sillas'],
            ['codigo' => '1200-1-03', 'subcategoria' => 'Mobiliario y Equipos de Oficina', 'clase' => '3', 'nombre' => 'Archivadores'],
            ['codigo' => '1200-1-04', 'subcategoria' => 'Mobiliario y Equipos de Oficina', 'clase' => '4', 'nombre' => 'Mesas de reunión'],
            ['codigo' => '1200-1-05', 'subcategoria' => 'Mobiliario y Equipos de Oficina', 'clase' => '5', 'nombre' => 'Estanterías'],
            ['codigo' => '1300-1-01', 'subcategoria' => 'Equipos de Tecnología', 'clase' => '1', 'nombre' => 'Computadoras de escritorio'],
            ['codigo' => '1300-1-02', 'subcategoria' => 'Equipos de Tecnología', 'clase' => '2', 'nombre' => 'Laptops'],
            ['codigo' => '1300-1-03', 'subcategoria' => 'Equipos de Tecnología', 'clase' => '3', 'nombre' => 'Impresoras'],
            ['codigo' => '1300-1-04', 'subcategoria' => 'Equipos de Tecnología', 'clase' => '4', 'nombre' => 'Servidores'],
            ['codigo' => '1300-1-05', 'subcategoria' => 'Equipos de Tecnología', 'clase' => '5', 'nombre' => 'Proyectores'],
            ['codigo' => '1300-1-06', 'subcategoria' => 'Equipos de Tecnología', 'clase' => '6', 'nombre' => 'Switches y routers'],
            ['codigo' => '1300-1-07', 'subcategoria' => 'Equipos de Tecnología', 'clase' => '7', 'nombre' => 'Teléfonos IP'],
            ['codigo' => '1400-1-01', 'subcategoria' => 'Vehículos y Transporte', 'clase' => '1', 'nombre' => 'Camionetas pickup'],
            ['codigo' => '1400-1-02', 'subcategoria' => 'Vehículos y Transporte', 'clase' => '2', 'nombre' => 'Vehículos sedán'],
            ['codigo' => '1400-1-03', 'subcategoria' => 'Vehículos y Transporte', 'clase' => '3', 'nombre' => 'Motocicletas'],
            ['codigo' => '1400-1-04', 'subcategoria' => 'Vehículos y Transporte', 'clase' => '4', 'nombre' => 'Camiones de recolección'],
            ['codigo' => '1400-1-05', 'subcategoria' => 'Vehículos y Transporte', 'clase' => '5', 'nombre' => 'Tractores viales'],
            ['codigo' => '1400-1-06', 'subcategoria' => 'Vehículos y Transporte', 'clase' => '6', 'nombre' => 'Botes o lanchas'],
            ['codigo' => '1500-1-01', 'subcategoria' => 'Maquinaria y Equipos Técnicos', 'clase' => '1', 'nombre' => 'Retroexcavadoras'],
            ['codigo' => '1500-1-02', 'subcategoria' => 'Maquinaria y Equipos Técnicos', 'clase' => '2', 'nombre' => 'Compactadoras'],
            ['codigo' => '1500-1-03', 'subcategoria' => 'Maquinaria e Equipos Técnicos', 'clase' => '3', 'nombre' => 'Bombas de agua'],
            ['codigo' => '1500-1-04', 'subcategoria' => 'Maquinaria y Equipos Técnicos', 'clase' => '4', 'nombre' => 'Equipos de iluminación pública'],
            ['codigo' => '1500-1-05', 'subcategoria' => 'Maquinaria y Equipos Técnicos', 'clase' => '5', 'nombre' => 'Equipos de poda y jardinería'],
            ['codigo' => '1600-1-01', 'subcategoria' => 'Equipos Médicos y de Salud', 'clase' => '1', 'nombre' => 'Camas hospitalarias'],
            ['codigo' => '1600-1-02', 'subcategoria' => 'Equipos Médicos y de Salud', 'clase' => '2', 'nombre' => 'Autoclaves'],
            ['codigo' => '1600-1-03', 'subcategoria' => 'Equipos Médicos y de Salud', 'clase' => '3', 'nombre' => 'Equipos de rayos X portátiles'],
            ['codigo' => '1600-1-04', 'subcategoria' => 'Equipos Médicos y de Salud', 'clase' => '4', 'nombre' => 'Refrigeradores de vacunas'],
            ['codigo' => '1700-1-01', 'subcategoria' => 'Equipos de Seguridad y Emergencia', 'clase' => '1', 'nombre' => 'Chalecos reflectivos'],
            ['codigo' => '1700-1-02', 'subcategoria' => 'Equipos de Seguridad y Emergencia', 'clase' => '2', 'nombre' => 'Extintores'],
            ['codigo' => '1700-1-03', 'subcategoria' => 'Equipos de Seguridad y Emergencia', 'clase' => '3', 'nombre' => 'Radios de comunicación'],
            ['codigo' => '1700-1-04', 'subcategoria' => 'Equipos de Seguridad y Emergencia', 'clase' => '4', 'nombre' => 'Cascos y uniformes'],
            ['codigo' => '1800-1-01', 'subcategoria' => 'Obras de Arte y Bienes Culturales', 'clase' => '1', 'nombre' => 'Estatuas y monumentos'],
            ['codigo' => '1800-1-02', 'subcategoria' => 'Obras de Arte y Bienes Culturales', 'clase' => '2', 'nombre' => 'Cuadros institucionales'],
            ['codigo' => '1900-01-01', 'subcategoria' => 'Otros Activos Fijos', 'clase' => '1', 'nombre' => 'Banderas nacionales'],
            ['codigo' => '1900-01-02', 'subcategoria' => 'Otros Activos Fijos', 'clase' => '2', 'nombre' => 'Relojes de pared institucionales'],
            ['codigo' => '1900-01-03', 'subcategoria' => 'Otros Activos Fijos', 'clase' => '3', 'nombre' => 'Equipos de sonido para eventos'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(['codigo' => $categoria['codigo']], $categoria);
        }
    }
}
