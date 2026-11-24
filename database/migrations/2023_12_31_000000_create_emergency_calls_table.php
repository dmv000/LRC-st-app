<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmergencyCallsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'emergency_calls';

    /**
     * Run the migrations.
     * @table emergency_calls
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->timestamp('call_time')->nullable()->default(null);
            $table->timestamp('dep_to_site')->nullable()->default(null);
            $table->timestamp('arrival_on_site')->nullable()->default(null);
            $table->timestamp('dep_to_dest')->nullable()->default(null);
            $table->timestamp('arrival_to_dest')->nullable()->default(null);
            $table->timestamp('unit_ready')->nullable()->default(null);
            $table->timestamp('return_to_station')->nullable()->default(null);
            $table->string('emergency_type', 10)->nullable()->default(null);
            $table->string('emergency_source', 100)->nullable()->default(null);
            $table->string('emergency_source_phone', 20)->nullable()->default(null);
            $table->string('someone_waiting', 200)->nullable()->default(null);
            $table->string('cause_of_incident', 200)->nullable()->default(null);
            $table->integer('from_type')->nullable()->default(null);
            $table->string('from_value', 100)->nullable()->default(null);
            $table->integer('to_type')->nullable()->default(null);
            $table->string('to_value', 100)->nullable()->default(null);
            $table->string('location_info', 200)->nullable()->default(null);
            $table->string('cases', 200)->nullable()->default(null);
            $table->integer('car_number')->nullable()->default(null);
            $table->integer('driver')->nullable()->default(null);
            $table->integer('team_leader')->nullable()->default(null);
            $table->string('other_emts', 100)->nullable()->default(null);
            $table->timestamp('anyway_time')->nullable()->default(null);
            $table->string('anyway_resp', 100)->nullable()->default(null);
            $table->timestamp('dc_time')->nullable()->default(null);
            $table->string('dc_resp', 100)->nullable()->default(null);
            $table->timestamp('ambulance_time')->nullable()->default(null);
            $table->string('ambulance_resp', 100)->nullable()->default(null);
            $table->timestamp('sar_time')->nullable()->default(null);
            $table->string('sar_resp', 100)->nullable()->default(null);
            $table->string('support_info', 200)->nullable()->default(null);
            $table->tinyInteger('annuler')->nullable()->default(null);
            $table->string('annuler_reason', 100)->nullable()->default(null);
            $table->string('annuler_source', 100)->nullable()->default(null);
            $table->timestamp('annuler_time')->nullable()->default(null);
            $table->integer('dispatch_1')->nullable()->default(null);
            $table->integer('dispatch_2')->nullable()->default(null);
            $table->string('or_1', 100)->nullable()->default(null);
            $table->string('or_2', 100)->nullable()->default(null);
            $table->integer('leader')->nullable()->default(null);
            $table->string('comment', 200)->nullable()->default(null);
            $table->integer('created_by')->nullable()->default(null);
            $table->timestamp('transport_time')->nullable()->default(null);
            $table->string('transport_type', 2)->nullable()->default(null);
            $table->tinyInteger('gamma')->nullable()->default(null);
            $table->string('admission', 100)->nullable()->default(null);
            $table->string('doctor', 100)->nullable()->default(null);
            $table->string('cleared_ready', 100)->nullable()->default(null);
            $table->string('reason_for_ambulance', 200)->nullable()->default(null);
            $table->integer('o2')->nullable()->default(null);
            $table->string('matelas', 200)->nullable()->default(null);
            $table->string('couveuse', 200)->nullable()->default(null);
            $table->string('respirateur', 200)->nullable()->default(null);
            $table->integer('is_deleted')->nullable()->default('0');
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
