<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sensor_datas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id');
            $table->float('pressure')->nullable();      // Tekanan air
            $table->float('flow_rate')->nullable();     // Debit air
            $table->float('water_level')->nullable();   // Ketinggian air
            $table->float('turbidity')->nullable();     // Kekeruhan
            $table->timestamp('timestamp');             // Waktu pengukuran
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_datas');
    }
};
