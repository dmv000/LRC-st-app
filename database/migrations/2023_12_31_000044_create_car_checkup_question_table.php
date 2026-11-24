<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCheckupQuestionTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'car_checkup_question';

    /**
     * Run the migrations.
     * @table car_checkup_question
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('title_english', 50);
            $table->string('title_arabic', 50);
            $table->integer('car_checkup_question_type_id');
            $table->string('default_value', 50);
            $table->integer('order');
            $table->integer('car_checkup_id');
            $table->integer('is_deleted')->default('0');
            $table->integer('version');
            $table->integer('min')->nullable()->default(null);
            $table->integer('max')->nullable()->default(null);
            $table->integer('step')->nullable()->default(null);
            $table->mediumText('image_source')->nullable()->default(null);

            $table->unique(["title_english", "title_arabic", "car_checkup_id", "version"], 'title_english');

            $table->index(["car_checkup_question_type_id"], 'car_checkup_question_type_id');

            $table->index(["car_checkup_id"], 'car_checkup_id');
            $table->timestamps();


            $table->foreign('car_checkup_id', $this->tableName . '_car_checkup_id')
                ->references('id')->on('car_checkup')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('car_checkup_question_type_id', $this->tableName . '_car_checkup_question_type_id')
                ->references('id')->on('car_checkup_question_type')
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
