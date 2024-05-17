<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('ticket_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('seat_count');
            $table->boolean('checked')->default(false);
            $table->timestamps();

            $table->foreign('bus_id')->references('id')->on('busses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_bookings');
    }
}
