<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCheckupDetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'car_checkup_details';

    /**
     * Run the migrations.
     * @table car_checkup_details
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('value');
            $table->integer('car_checkup_header_id');
            $table->integer('car_checkup_question_id');

            $table->index(["car_checkup_header_id"], 'car_checkup_header_id');

            $table->index(["car_checkup_question_id"], 'car_checkup_question_id');
            $table->timestamps();


            $table->foreign('car_checkup_header_id', $this->tableName . '_car_checkup_header_id')
                ->references('id')->on('car_checkup_header')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('car_checkup_question_id', $this->tableName . '_car_checkup_question_id')
                ->references('id')->on('car_checkup_question')
                ->onDelete('restrict')
                ->onUpdate('restrict');
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
