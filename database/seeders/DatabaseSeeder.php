<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                // 1. Tablas de Catálogo (Lookup Tables)
            InstitucionSeeder::class,
            RoleSeeder::class,
            CargoSeeder::class,
            ColorSeeder::class,
            MarcaSeeder::class,
            EstadoActivoSeeder::class,
            FuenteSeeder::class,
            DepartamentoSeeder::class,
            CategoriaSeeder::class,

                // 2. Usuarios y Responsables Iniciales
            CustodioSeeder::class,
            UserSeeder::class,

                // 3. Tablas de Entidades Dependientes
            ProveedorSeeder::class,
            UbicacionSeeder::class,
            PersonalResponsableSeeder::class,
            ModeloSeeder::class,
            TecnicoSeeder::class,

                // 4. Tablas de Activos Principales
            ActivoFijoSeeder::class,
            VehiculoSeeder::class,
            TerrenoSeeder::class,

                // 5. Tablas Transaccionales
            MovimientoSeeder::class,
            MantenimientoSeeder::class,
            BajaActivoSeeder::class,

                // 6. Tablas de Inventario
            InventarioSeeder::class,
            InventarioDetalleSeeder::class,
        ]);
    }
}
