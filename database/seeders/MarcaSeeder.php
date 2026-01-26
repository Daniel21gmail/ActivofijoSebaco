<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Toyota'],
            ['nombre' => 'Nissan'],
            ['nombre' => 'Honda'],
            ['nombre' => 'Hyundai'],
            ['nombre' => 'Kia'],
            ['nombre' => 'Dell'],
            ['nombre' => 'HP'],
            ['nombre' => 'Lenovo'],
            ['nombre' => 'Samsung'],
            ['nombre' => 'LG'],
        ];

        foreach ($marcas as $marca) {
            \App\Models\Marca::firstOrCreate($marca);
        }
    }
}
