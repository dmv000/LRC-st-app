<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialLogTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'material_log';

    /**
     * Run the migrations.
     * @table material_log
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->default(null);
            $table->integer('material_id')->nullable()->default(null);
            $table->integer('amount')->nullable()->default(null);

            $table->index(["material_id"], 'material_id');

            $table->index(["user_id"], 'user_id');
            $table->timestamps();


            $table->foreign('material_id', $this->tableName . '_material_id')
                ->references('id')->on('material')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('user_id', $this->tableName . '_user_id')
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
