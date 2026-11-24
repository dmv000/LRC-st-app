<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyValuesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'survey_values';

    /**
     * Run the migrations.
     * @table survey_values
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('survey_id');
            $table->integer('field_id');
            $table->string('value')->nullable()->default(null);
            $table->string('comment')->default('');

            $table->index(["survey_id"], 'survey_id');

            $table->index(["field_id"], 'field_id');
            $table->timestamps();


            $table->foreign('field_id', $this->tableName . '_field_id')
                ->references('id')->on('survey_fields')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('survey_id', $this->tableName . '_survey_id')
                ->references('id')->on('survey')
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
