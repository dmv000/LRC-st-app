<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('name')->default('Error');
            $table->string('nickname', 128)->default('Error');
            $table->string('email')->default('Error');
            $table->string('mobile', 128)->default('Error');
            $table->string('objectsid', 128);
            $table->tinyInteger('is_deleted')->default('0');
            $table->tinyInteger('driver')->default('0');
            $table->string('remember_token', 128);

            $table->unique(["nickname"], 'username');

            $table->unique(["objectsid"], 'objectsid');
            $table->nullableTimestamps();
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
