<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegisterChangeLogTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'register_change_log';

    /**
     * Run the migrations.
     * @table register_change_log
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('user_id');
            $table->integer('shift_id');
            $table->string('type', 100)->nullable()->default(null);
            $table->string('new')->nullable()->default(null);
            $table->string('old')->nullable()->default(null);

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
