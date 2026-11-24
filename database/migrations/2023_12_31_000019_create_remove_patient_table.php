<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemovePatientTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'remove_patient';

    /**
     * Run the migrations.
     * @table remove_patient
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->integer('date_of_birth');
            $table->string('phone_number', 20);
            $table->string('house_location', 150);
            $table->integer('nationality');
            $table->text('sex');
            $table->string('father_name', 50);
            $table->string('emergency_contact', 50);
            $table->string('emergency_name', 50);
            $table->string('passenger_name', 50);

            $table->index(["nationality"], 'nationality');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
