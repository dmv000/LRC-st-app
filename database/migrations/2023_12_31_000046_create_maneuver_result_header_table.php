<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManeuverResultHeaderTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'maneuver_result_header';

    /**
     * Run the migrations.
     * @table maneuver_result_header
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('maneuver_id')->nullable()->default(null);
            $table->integer('created_by')->nullable()->default(null);
            $table->date('date')->nullable()->default(null);
            $table->time('start_time')->nullable()->default(null);
            $table->time('end_time')->nullable()->default(null);
            $table->integer('mission_leader')->nullable()->default(null);
            $table->string('emts', 128)->nullable()->default(null);
            $table->tinyInteger('is_deleted')->default('0');
            $table->tinyInteger('is_finished')->default('0');

            $table->index(["maneuver_id"], 'maneuver_id');

            $table->index(["created_by"], 'created_by');
            $table->timestamps();


            $table->foreign('maneuver_id', $this->tableName . '_maneuver_id')
                ->references('id')->on('maneuver')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('created_by', $this->tableName . '_created_by')
                ->references('id')->on('users')
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
