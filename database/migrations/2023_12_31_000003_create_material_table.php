<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'material';

    /**
     * Run the migrations.
     * @table material
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->string('name', 128)->nullable()->default(null);
            $table->integer('amount')->nullable()->default('0');
            $table->integer('criticalAmount')->nullable()->default('1');
            $table->string('comment')->nullable()->default('');
            $table->tinyInteger('is_deleted')->nullable()->default('0');
            $table->integer('kiosk')->nullable()->default('0');
            $table->integer('kiosk_count')->nullable()->default('0');
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
