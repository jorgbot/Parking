<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkings', function (Blueprint $table) {
            $table->increments('parking_id');
            $table->string('name',50);
            $table->string('address', 60);
            $table->integer('status')->unsigned();
            $table->integer('type')->unsigned();
            $table->integer('motorcycles_num')->unsigned()->nullable();
            $table->integer('cars_num')->unsigned()->nullable();
            $table->integer('hour_motorcycles_price')->unsigned()->nullable();
            $table->integer('monthly_motorcycles_price')->unsigned()->nullable();
            $table->integer('day_motorcycles_price')->unsigned()->nullable();
            $table->integer('hour_cars_price')->unsigned()->nullable();
            $table->integer('monthly_cars_price')->unsigned()->nullable();
            $table->integer('day_cars_price')->unsigned()->nullable();
            $table->integer('free_time')->unsigned()->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('parkings');
    }
}
