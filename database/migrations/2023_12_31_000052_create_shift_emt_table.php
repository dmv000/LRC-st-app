<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftEmtTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'shift_emt';

    /**
     * Run the migrations.
     * @table shift_emt
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
            $table->string('arrival', 10)->nullable()->default(null);
            $table->string('departure', 10)->nullable()->default(null);
            $table->string('checkup')->nullable()->default(null);
            $table->integer('shift_status_id');
            $table->tinyInteger('is_deleted')->nullable()->default('0');
            $table->tinyInteger('double_s')->nullable()->default('0');
            $table->integer('badal')->nullable()->default('-1');
            $table->string('work_description')->nullable()->default(null);

            $table->index(["user_id"], 'user_id');

            $table->index(["shift_status_id"], 'shift_status_id');

            $table->index(["shift_id"], 'shift_id');
            $table->timestamps();


            $table->foreign('shift_id', $this->tableName . '_shift_id')
                ->references('id')->on('register')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('shift_status_id', $this->tableName . '_shift_status_id')
                ->references('id')->on('shift_status')
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
