<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManeuverResultDetailsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'maneuver_result_details';

    /**
     * Run the migrations.
     * @table maneuver_result_details
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('maneuver_result_id');
            $table->integer('maneuver_section_id');
            $table->string('comment')->default('');

            $table->index(["maneuver_result_id"], 'maneuver_result_id');

            $table->index(["maneuver_section_id"], 'maneuver_section_id');
            $table->timestamps();


            $table->foreign('maneuver_result_id', $this->tableName . '_maneuver_result_id')
                ->references('id')->on('maneuver_result_header')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('maneuver_section_id', $this->tableName . '_maneuver_section_id')
                ->references('id')->on('maneuver_section')
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
