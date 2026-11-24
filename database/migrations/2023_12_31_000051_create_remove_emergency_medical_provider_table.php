<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemoveEmergencyMedicalProviderTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'remove_emergency_medical_provider';

    /**
     * Run the migrations.
     * @table remove_emergency_medical_provider
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone_number', 20);
            $table->string('nickname', 20);
            $table->date('date_of_birth');
            $table->integer('grade');
            $table->integer('id', true);
            $table->string('email', 50);
            $table->integer('station')->nullable()->default(null);
            $table->integer('joining_year');
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->tinyInteger('driver');
            $table->tinyInteger('deleted')->default('0');
            $table->tinyInteger('excludeB')->default('0');

            $table->index(["grade"], 'grade');

            $table->index(["station"], 'station');
            $table->timestamps();


            $table->foreign('station', $this->tableName . '_station')
                ->references('id')->on('station')
                ->onDelete('set null')
                ->onUpdate('set null');
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
