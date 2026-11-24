<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManeuverQuestionTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'maneuver_question';

    /**
     * Run the migrations.
     * @table maneuver_question
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('section_id')->nullable()->default(null);
            $table->string('text');
            $table->enum('type', ['text', 'boolean', 'empty', 'title']);
            $table->tinyInteger('is_deleted')->default('0');
            $table->tinyInteger('has_comment')->default('0');
            $table->integer('orderr')->nullable()->default('0');

            $table->index(["section_id"], 'section_id');
            $table->timestamps();


            $table->foreign('section_id', $this->tableName . '_section_id')
                ->references('id')->on('maneuver_section')
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
