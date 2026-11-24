<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelStationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('fuel_stations')->insert([
            'name' => 'Station S',
            'weight' => 1
        ]);
    }
}
