<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'comment';

    /**
     * Run the migrations.
     * @table comment
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->date('date');
            $table->string('content')->nullable()->default(null);
            $table->integer('from')->nullable()->default(null);
            $table->string('to', 100)->nullable()->default('');

            $table->index(["from"], 'from');
            $table->timestamps();


            $table->foreign('from', $this->tableName . '_from')
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
