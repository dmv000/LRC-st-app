<?php

namespace App\Http\ORM;

use Eloquent;
class CarCheckupDetails extends Eloquent
{
    protected $connection = 'mysql';
    protected $table = "car_checkup_details";
}
