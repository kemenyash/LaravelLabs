<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CarsTableMock extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('cars')->insert([
            ['owner' => 'New Torrancemouth', 'brand' => 'Licensed', 'license_plate' => 'BLAND658', 'color' => 'hack'],
            ['owner' => 'Vasyl Kondratenko', 'brand' => 'Ford', 'license_plate' => 'AO5678CC', 'color' => 'White'],
            ['owner' => 'Mariya Ionova', 'brand' => 'Chrysler', 'license_plate' => 'AO9876DD', 'color' => 'Green'],
            ['owner' => 'Mykhalina Shevchenko', 'brand' => 'Buick', 'license_plate' => 'BC4567EE', 'color' => 'Red'],
        ]);
    }
}
