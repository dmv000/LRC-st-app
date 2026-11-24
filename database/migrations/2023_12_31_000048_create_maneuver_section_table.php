<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManeuverSectionTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'maneuver_section';

    /**
     * Run the migrations.
     * @table maneuver_section
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('title', 128);
            $table->integer('maneuver_id')->nullable()->default(null);
            $table->tinyInteger('is_deleted')->default('0');
            $table->integer('orderr')->nullable()->default('0');

            $table->index(["maneuver_id"], 'maneuver_id');
            $table->timestamps();


            $table->foreign('maneuver_id', $this->tableName . '_maneuver_id')
                ->references('id')->on('maneuver')
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
