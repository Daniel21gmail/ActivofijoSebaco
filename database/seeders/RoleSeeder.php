<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['nombre' => 'Administrador']);
        Role::create(['nombre' => 'Consulta']);
        Role::create(['nombre' => 'Operador']);
        Role::create(['nombre' => 'Auxiliar']);
        Role::create(['nombre' => 'Auditor']);
        Role::create(['nombre' => 'Jefe de Departamento']);
        Role::create(['nombre' => 'Secretaria']);
        Role::create(['nombre' => 'Técnico']);
        Role::create(['nombre' => 'Custodio']);
        Role::create(['nombre' => 'Supervisor']);
    }
}
