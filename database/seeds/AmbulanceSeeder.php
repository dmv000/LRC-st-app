<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmbulanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ambulance')->insert([[
            'car_number' => 576,
            'is_deleted' => 0,
            'is_broken' => 0,
            'station' => 502
        ], [
            'car_number' => 578,
            'is_deleted' => 0,
            'is_broken' => 0,
            'station' => 502
        ]]);
    }
}
