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
         DB::table('vehicles')->insert([
            ['code' => 1, 'owner' => 'New Torrancemouth', 'brand' => 'Licensed', 'license_plate' => 'BLAND658', 'color' => 'hack'],
            ['code' => 2, 'owner' => 'Vasyl Kondratenko', 'brand' => 'Ford', 'license_plate' => 'AO5678CC', 'color' => 'White'],
            ['code' => 3, 'owner' => 'Mariya Ionova', 'brand' => 'Chrysler', 'license_plate' => 'AO9876DD', 'color' => 'Green'],
            ['code' => 4, 'owner' => 'Mykhalina Shevchenko', 'brand' => 'Buick', 'license_plate' => 'BC4567EE', 'color' => 'Red'],
        ]);
    }
}
