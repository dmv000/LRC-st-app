<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionMaterialTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'mission_material';

    /**
     * Run the migrations.
     * @table mission_material
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('mission_id');
            $table->integer('material_id');
            $table->integer('id', true);

            $table->index(["material_id"], 'mission_material');

            $table->index(["mission_id"], 'mission_material_');


            $table->foreign('material_id', $this->tableName . '_mission_material')
                ->references('id')->on('material')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('mission_id', $this->tableName . '_mission_material_')
                ->references('id')->on('urgence')
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
