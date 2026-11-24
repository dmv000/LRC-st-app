<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionDriverTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'mission_driver';

    /**
     * Run the migrations.
     * @table mission_driver
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('mission_id');
            $table->integer('driver_id');

            $table->index(["mission_id", "driver_id"], 'mission_id');

            $table->index(["driver_id"], 'driver_id');
            $table->timestamps();


            $table->foreign('driver_id', $this->tableName . '_driver_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('mission_id', $this->tableName . '_mission_id')
                ->references('id')->on('urgence')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
