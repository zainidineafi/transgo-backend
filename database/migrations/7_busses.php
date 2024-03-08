<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('busses', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('license_plate_number', 13); // maksimal 13 karakter
            $table->integer('chair')->unsigned(); // maksimal 2 kursi
            $table->enum('class', ['ekonomi', 'bisnis']); // pilihan ekonomi atau bisnis
            $table->float('price');
            $table->foreignId('driver_id'); // ambil id driver dari tabel drivers
            $table->foreignId('bus_conductor_id'); // ambil id dari tabel bus_conductors
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('users');
            $table->foreign('bus_conductor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('busses');
    }
};
