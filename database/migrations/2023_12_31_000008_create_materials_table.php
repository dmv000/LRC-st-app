<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'materials';

    /**
     * Run the migrations.
     * @table materials
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id', true);
            $table->string('description', 50);
            $table->integer('count')->default('2');
            $table->integer('criticalCount')->default('1');
            $table->integer('kiosk')->nullable()->default('0');
            $table->integer('kiosk_count')->nullable()->default('0');

            $table->unique(["description"], 'description');
            $table->timestamps();
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
