<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionEmtsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'mission_emts';

    /**
     * Run the migrations.
     * @table mission_emts
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('mission_id')->default(null);
            $table->integer('emt_id')->default(null);

            $table->index(["mission_id"], 'mission_id');

            $table->index(["emt_id"], 'emt_id');
            $table->timestamps();


            $table->foreign('mission_id', $this->tableName . '_mission_id')
                ->references('id')->on('urgence')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('emt_id', $this->tableName . '_emt_id')
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
