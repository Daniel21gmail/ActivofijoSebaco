<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelos = [
            ['nombre' => 'Hilux', 'marca_id' => 1], // Toyota
            ['nombre' => 'Frontier', 'marca_id' => 2], // Nissan
            ['nombre' => 'Civic', 'marca_id' => 3], // Honda
            ['nombre' => 'Tucson', 'marca_id' => 4], // Hyundai
            ['nombre' => 'Sportage', 'marca_id' => 5], // Kia
            ['nombre' => 'Latitude 5420', 'marca_id' => 6], // Dell
            ['nombre' => 'ProBook 450', 'marca_id' => 7], // HP
            ['nombre' => 'ThinkPad E15', 'marca_id' => 8], // Lenovo
            ['nombre' => 'Galaxy Tab A8', 'marca_id' => 9], // Samsung
            ['nombre' => '27MK430H', 'marca_id' => 10], // LG
        ];

        DB::table('modelos')->insert($modelos);
    }
}
