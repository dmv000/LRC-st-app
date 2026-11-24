<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionTypeTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'mission_type';

    /**
     * Run the migrations.
     * @table mission_type
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('name', 50);
            $table->integer('mission_subtype')->nullable()->default(null);

            $table->index(["name"], 'name');

            $table->index(["mission_subtype"], 'mission_subtype');


            $table->foreign('mission_subtype', $this->tableName . '_mission_subtype')
                ->references('id')->on('mission_subtype')
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
