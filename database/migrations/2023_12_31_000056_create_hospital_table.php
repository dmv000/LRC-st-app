<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'hospital';

    /**
     * Run the migrations.
     * @table hospital
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('name', 50);
            $table->string('code', 50);
            $table->integer('station')->nullable()->default(null);
            $table->integer('weight');
            $table->double('longitude')->nullable()->default(null);
            $table->double('latitude')->nullable()->default(null);
            $table->integer('created_by')->nullable()->default(null);

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
