<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEdjDtlTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'edj_dtl';

    /**
     * Run the migrations.
     * @table edj_dtl
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('edj_hdr_id');
            $table->date('date');
            $table->enum('type', ['AM Shift', 'PM Shift', 'Technical AM Shift', 'Technical PM Shift'])->nullable()->default(null);
            $table->enum('type_bkp', ['AM Shift', 'PM Shift'])->nullable()->default(null);

            $table->index(["edj_hdr_id"], 'edj_hdr_id');
            $table->timestamps();


            $table->foreign('edj_hdr_id', $this->tableName . '_edj_hdr_id')
                ->references('id')->on('edj_hdr')
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
