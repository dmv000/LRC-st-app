<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionDocumentTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'mission_document';

    /**
     * Run the migrations.
     * @table mission_document
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('added_by');
            $table->integer('mission_id');
            $table->tinyInteger('is_deleted')->default('0');
            $table->string('name');

            $table->unique(["name"], 'name');

            $table->index(["added_by"], 'added_by');

            $table->index(["mission_id"], 'mission_id');
            $table->timestamps();


            $table->foreign('mission_id', $this->tableName . '_mission_id')
                ->references('id')->on('urgence')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('added_by', $this->tableName . '_added_by')
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
