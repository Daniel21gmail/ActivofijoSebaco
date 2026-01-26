<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('nombre', 'Administrador')->first();
        $consultaRole = Role::where('nombre', 'Consulta')->first();
        $operadorRole = Role::where('nombre', 'Operador')->first();
        $tecnicoRole = Role::where('nombre', 'Técnico')->first();

        User::create([
            'nombre' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'rol' => 'Administrador',
        ]);

        $users = [
            ['nombre' => 'Juan Pérez', 'email' => 'juan.perez@example.com', 'role_id' => $operadorRole->id, 'rol' => 'Operador'],
            ['nombre' => 'María López', 'email' => 'maria.lopez@example.com', 'role_id' => $consultaRole->id, 'rol' => 'Consulta'],
            ['nombre' => 'Carlos Gómez', 'email' => 'carlos.gomez@example.com', 'role_id' => $tecnicoRole->id, 'rol' => 'Técnico'],
            ['nombre' => 'Ana Martínez', 'email' => 'ana.martinez@example.com', 'role_id' => $operadorRole->id, 'rol' => 'Operador'],
            ['nombre' => 'Roberto Rodríguez', 'email' => 'roberto.r@example.com', 'role_id' => $consultaRole->id, 'rol' => 'Consulta'],
            ['nombre' => 'Elena Hernández', 'email' => 'elena.h@example.com', 'role_id' => $tecnicoRole->id, 'rol' => 'Técnico'],
            ['nombre' => 'Miguel Flores', 'email' => 'miguel.f@example.com', 'role_id' => $operadorRole->id, 'rol' => 'Operador'],
            ['nombre' => 'Patricia Morales', 'email' => 'patricia.m@example.com', 'role_id' => $consultaRole->id, 'rol' => 'Consulta'],
            ['nombre' => 'José Ramírez', 'email' => 'jose.r@example.com', 'role_id' => $tecnicoRole->id, 'rol' => 'Técnico'],
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'password' => Hash::make('password'),
            ]));
        }
    }
}
