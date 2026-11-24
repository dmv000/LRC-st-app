<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('fuel_types')->insert(
            [
                [
                    'description' => 'Diesel'
                ],
                [
                    'description' => 'Petrol'
                ]
            ]
        );
    }
}
