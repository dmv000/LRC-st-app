<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('event_type')->insert([
            ['description' => 'Log In'],
            ['description' => 'Log Out'],
            ['description' => 'Add Village'],
            ['description' => 'Add Fuel Station'],
            ['description' => 'Add Emergency'],
            ['description' => 'Add Fuel Coupon'],
            ['description' => 'Edit Emergency'],
            ['description' => 'Edit Fuel Coupon'],
            ['description' => 'Add Edj Shifts'],
            ['description' => 'Mobile Log In'],
            ['description' => 'View Maneuver'],
            ['description' => 'Create Maneuver'],
            ['description' => 'Edit Maneuver'],
            ['description' => 'View Material Checkup Online'],
            ['description' => 'Edit Material Checkup Online'],
            ['description' => 'View Upload Mission Document'],
            ['description' => 'Upload Mission Document'],
            ['description' => 'Add Fuel Coupon View'],
            ['description' => 'Unauthorized Access'],
            ['description' => 'Add/Edit Register'],
            ['description' => 'Reset Password'],
            ['description' => 'Add/Edit Car Checkup'],
            ['description' => 'Hospital Contact'],
            ['description' => 'Delete Emergency Call'],
            ['description' => 'Add Car Checkup'],
            ['description' => 'Save Car Checkup'],
            ['description' => 'Add Hospital'],
            ['description' => 'Change note from [{$point->application_notes}] to [{$application_notes}]'],
            ['description' => 'Change date from [{$point->application_date}] to [{$application_date}]'],
            ['description' => 'Remove User Point [{$user_point}]'],
        ]);
    }
}
