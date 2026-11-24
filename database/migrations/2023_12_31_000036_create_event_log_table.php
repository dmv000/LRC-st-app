<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventLogTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'event_log';

    /**
     * Run the migrations.
     * @table event_log
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('user_id');
            $table->integer('event_type');
            $table->string('details')->nullable()->default('');

            $table->index(["user_id"], 'user_id');

            $table->index(["event_type"], 'event_type');
            $table->timestamps();


            $table->foreign('event_type', $this->tableName . '_event_type')
                ->references('id')->on('event_type')
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
