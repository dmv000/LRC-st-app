<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('app_settings')->insert([
            'description' => 'Fuel report go back days',
            'name' => 'fuel_report_go_back',
            'value' => 5
        ]);
    }
}
