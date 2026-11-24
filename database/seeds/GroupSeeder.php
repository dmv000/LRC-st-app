<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('group')->insert([[
            'id' => 2,
            'description' => 'Drivers Admin',
            'address' => 'cn=CN502_Drivers_Admin,ou=Groups,ou=C502,ou=LRC Stations,dc=lrc,dc=local',
            'parent_group' => null
        ], [
            'id' => 3,
            'description' => 'Drivers',
            'address' => 'cn=CN502_Drivers,ou=Groups,ou=C502,ou=LRC Stations,dc=lrc,dc=local',
            'parent_group' => 2
        ]]);
    }
}
