<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('ticket_id');
            $table->dateTime('hour');
            $table->string('plate', 10);
            $table->integer('status')->unsigned();
            $table->integer('type')->unsigned();
            $table->integer('price')->unsigned()->nullable();
            $table->integer('schedule')->unsigned()->nullable();
            $table->integer('parking_id')->unsigned();
            $table->integer('partner_id')->unsigned();
            $table->string('drawer', 10)->nullable();
            $table->string('name', 70)->nullable();
            $table->dateTime('date_end')->nullable();
            $table->rememberToken();
            $table->timestamps();
            //$table->primary('partner_id');
            $table->foreign('parking_id')
                ->references('parking_id')->on('parkings')
                ->onDelete('cascade');
            $table->foreign('partner_id')
                ->references('partner_id')->on('partners')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
