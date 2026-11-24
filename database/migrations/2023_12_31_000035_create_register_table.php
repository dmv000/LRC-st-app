<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegisterTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'register';

    /**
     * Run the migrations.
     * @table register
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->date('register_date')->nullable()->default(null);
            $table->integer('created_by');
            $table->string('skills')->nullable()->default(null);
            $table->string('special_missions');
            $table->tinyInteger('finished')->nullable()->default(null);

            $table->index(["created_by"], 'created_by');
            $table->timestamps();


            $table->foreign('created_by', $this->tableName . '_created_by')
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
