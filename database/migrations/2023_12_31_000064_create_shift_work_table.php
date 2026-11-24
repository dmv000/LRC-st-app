<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftWorkTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'shift_work';

    /**
     * Run the migrations.
     * @table shift_work
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
            $table->integer('shift_emt_id');
            $table->integer('work_id');
            $table->tinyInteger('is_deleted')->nullable()->default('0');

            $table->index(["user_id"], 'user_id');

            $table->index(["shift_emt_id"], 'shift_emt_id');

            $table->index(["shift_id"], 'shift_id');

            $table->index(["work_id"], 'work_id');
            $table->timestamps();


            $table->foreign('shift_id', $this->tableName . '_shift_id')
                ->references('id')->on('register')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('shift_emt_id', $this->tableName . '_shift_emt_id')
                ->references('id')->on('shift_emt')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('user_id', $this->tableName . '_user_id')
                ->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('work_id', $this->tableName . '_work_id')
                ->references('id')->on('station_work')
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
