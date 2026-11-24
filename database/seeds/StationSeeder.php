<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('station')->insert([
            'id' => 502,
            'name' => 'Batroun',
            'district' => 500
        ]);
    }
}
