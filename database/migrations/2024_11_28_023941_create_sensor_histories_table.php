<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id'); // Gunakan unsignedBigInteger agar sesuai dengan id dari devices
            $table->float('temperature')->nullable(); // °C
            $table->float('humidity')->nullable();    // %
            $table->float('smoke')->nullable();       // ppm
            $table->boolean('motion')->nullable();    // 1 = detected, 0 = not detected
            $table->timestamp('recorded_at');
            $table->timestamps();

            // Menambahkan foreign key constraint
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_histories');
    }
}
