<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmbulanceTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'ambulance';

    /**
     * Run the migrations.
     * @table ambulance
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('car_number');
            $table->tinyInteger('is_deleted');
            $table->tinyInteger('is_broken');
            $table->integer('station')->nullable()->default(null);

            $table->unique(["car_number"], 'car_number');

            $table->index(["station"], 'station');


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
