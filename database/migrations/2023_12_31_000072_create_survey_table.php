<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'survey';

    /**
     * Run the migrations.
     * @table survey
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('guid', 32);
            $table->integer('user_id');
            $table->integer('mission_id');
            $table->tinyInteger('is_deleted')->default('0');
            $table->tinyInteger('is_filled')->default('0');
            $table->timestamp('filled_at')->nullable()->default(null);
            $table->tinyInteger('sent')->nullable()->default('0');

            $table->unique(["guid"], 'guid');

            $table->index(["user_id"], 'user_id');

            $table->index(["mission_id"], 'mission_id');
            $table->timestamps();


            $table->foreign('mission_id', $this->tableName . '_mission_id')
                ->references('id')->on('urgence')
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
