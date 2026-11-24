<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftTopicTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'shift_topic';

    /**
     * Run the migrations.
     * @table shift_topic
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('shift_id');
            $table->string('time', 100)->nullable()->default(null);
            $table->string('topic')->nullable()->default(null);
            $table->string('comments')->nullable()->default(null);
            $table->tinyInteger('is_deleted')->nullable()->default('0');

            $table->index(["shift_id"], 'shift_id');
            $table->timestamps();


            $table->foreign('shift_id', $this->tableName . '_shift_id')
                ->references('id')->on('register')
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
