<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPointTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'user_point';

    /**
     * Run the migrations.
     * @table user_point
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->default(null);
            $table->integer('point_action_id')->nullable()->default(null);
            $table->date('application_date')->nullable()->default(null);
            $table->string('application_notes')->nullable()->default(null);

            $table->index(["user_id"], 'FK_user_point_user_id');

            $table->index(["point_action_id"], 'FK_user_point_point_action_id');
            $table->nullableTimestamps();


            $table->foreign('point_action_id', $this->tableName . '_FK_user_point_point_action_id')
                ->references('id')->on('point_action')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('user_id', $this->tableName . '_FK_user_point_user_id')
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
