<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyFieldsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'survey_fields';

    /**
     * Run the migrations.
     * @table survey_fields
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('name');
            $table->enum('type', ['Bool', 'Text']);
            $table->integer('order');
            $table->string('arabic_description');
            $table->tinyInteger('is_deleted')->default('0');
            $table->string('default', 128)->nullable()->default('false');

            $table->index(["name"], 'name');
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
