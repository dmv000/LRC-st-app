<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftParticipantTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'shift_participant';

    /**
     * Run the migrations.
     * @table shift_participant
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('shift_id');
            $table->integer('user_id');
            $table->tinyInteger('is_deleted')->nullable()->default('0');

            $table->index(["user_id"], 'user_id');

            $table->index(["shift_id"], 'shift_id');
            $table->timestamps();


            $table->foreign('shift_id', $this->tableName . '_shift_id')
                ->references('id')->on('register')
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
