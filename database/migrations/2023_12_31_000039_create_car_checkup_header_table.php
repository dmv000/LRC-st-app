<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCheckupHeaderTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'car_checkup_header';

    /**
     * Run the migrations.
     * @table car_checkup_header
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('created_by');
            $table->integer('car_checkup_id');
            $table->timestamp('start_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('is_deleted')->default('0');
            $table->integer('version')->nullable()->default(null);
            $table->string('driver_name', 150)->nullable()->default(null);
            $table->date('checkup_date')->nullable()->default(null);

            $table->index(["car_checkup_id"], 'checkup_head_checkup');

            $table->index(["created_by"], 'checkup_head_user');
            $table->timestamps();


            $table->foreign('car_checkup_id', $this->tableName . '_checkup_head_checkup')
                ->references('id')->on('car_checkup')
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
