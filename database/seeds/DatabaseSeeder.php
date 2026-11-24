<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DistrictSeeder::class,
            StationSeeder::class,
            AmbulanceSeeder::class,
            EventTypeSeeder::class,
            GroupSeeder::class,
            FuelStationsSeeder::class,
            FuelTypesSeeder::class,
            AppSettingsSeeder::class
        ]);
    }
}
