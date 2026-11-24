<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelCouponTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'fuel_coupon';

    /**
     * Run the migrations.
     * @table fuel_coupon
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('car_id')->nullable()->default(null);
            $table->integer('driver_id')->nullable()->default(null);
            $table->tinyInteger('army');
            $table->date('date');
            $table->integer('fuel_station_id')->nullable()->default(null);
            $table->integer('fuel_type_id')->nullable()->default(null);
            $table->integer('short_coupon_number');
            $table->decimal('litre', 20, 3);
            $table->decimal('price', 20, 3);
            $table->integer('kms');
            $table->integer('created_by')->nullable()->default(null);
            $table->tinyInteger('is_deleted')->nullable()->default('0');
            $table->bigInteger('coupon_number')->nullable()->default(null);

            $table->index(["car_id"], 'car_id');

            $table->index(["driver_id"], 'driver_id');

            $table->index(["fuel_station_id"], 'fuel_station_id');

            $table->index(["fuel_type_id"], 'fuel_type');

            $table->index(["created_by"], 'created_by');
            $table->timestamps();


            $table->foreign('car_id', $this->tableName . '_car_id')
                ->references('id')->on('ambulance')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('created_by', $this->tableName . '_created_by')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('driver_id', $this->tableName . '_driver_id')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('fuel_station_id', $this->tableName . '_fuel_station_id')
                ->references('id')->on('fuel_stations')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('fuel_type_id', $this->tableName . '_fuel_type')
                ->references('id')->on('fuel_types')
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
