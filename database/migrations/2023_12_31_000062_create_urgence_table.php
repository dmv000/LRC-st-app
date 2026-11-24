<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrgenceTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'urgence';

    /**
     * Run the migrations.
     * @table urgence
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('ambulance_id')->nullable()->default(null);
            $table->string('patient_name')->nullable()->default(null);
            $table->integer('patient_age')->nullable()->default(null);
            $table->string('patient_case')->nullable()->default(null);
            $table->integer('type')->nullable()->default(null);
            $table->string('material_used')->nullable()->default(null);
            $table->string('from', 5)->nullable()->default(null);
            $table->string('to', 5)->nullable()->default(null);
            $table->string('o2')->nullable()->default(null);
            $table->timestamp('call_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('dep_to_site')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('arr_on_site')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('dep_from_site')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('arr_to_dest')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('unit_available')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('ret_to_centre')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('patient_sex', ['Male', 'Female', 'Trans(`you`re in too deep, stay out`)'])->nullable()->default(null);
            $table->integer('patient_nationality')->nullable()->default(null);
            $table->string('from_other')->nullable()->default(null);
            $table->string('to_Other')->nullable()->default(null);
            $table->integer('parent_id')->nullable()->default(null);
            $table->string('other_emts')->nullable()->default(null);
            $table->integer('created_by');
            $table->tinyInteger('is_deleted')->nullable()->default('0');
            $table->string('comment')->nullable()->default('');
            $table->string('phone', 20)->nullable()->default(null);
            $table->integer('checked')->nullable()->default('0');
            $table->integer('checked_by')->nullable()->default(null);
            $table->timestamp('checked_date')->nullable()->default(null);

            $table->index(["ambulance_id"], 'Ambulance_Id');

            $table->index(["type"], 'Type');

            $table->index(["created_by"], 'created_by');
            $table->timestamps();


            $table->foreign('ambulance_id', $this->tableName . '_Ambulance_Id')
                ->references('id')->on('ambulance')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('created_by', $this->tableName . '_created_by')
                ->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('type', $this->tableName . '_Type')
                ->references('id')->on('mission_type')
                ->onDelete('set null')
                ->onUpdate('cascade');
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
