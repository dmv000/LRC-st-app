<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManeuverResultDataTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'maneuver_result_data';

    /**
     * Run the migrations.
     * @table maneuver_result_data
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('man_res_id')->nullable()->default(null);
            $table->integer('man_question_id')->nullable()->default(null);
            $table->string('value')->nullable()->default('');
            $table->string('comment')->nullable()->default('');

            $table->index(["man_res_id"], 'man_res_id');

            $table->index(["man_question_id"], 'man_question_id');
            $table->timestamps();


            $table->foreign('man_question_id', $this->tableName . '_man_question_id')
                ->references('id')->on('maneuver_question')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('man_res_id', $this->tableName . '_man_res_id')
                ->references('id')->on('maneuver_result_details')
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
