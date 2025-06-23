<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flow_pressure_sensors', function (Blueprint $table) {
            $table->string('id')->primary(); // Menggunakan string untuk PK jika id di devices juga string (UUID/ULID)
            $table->string('device_id'); // Foreign key ke tabel devices
            $table->float('flow_rate');
            $table->float('pressure');
            $table->float('volume'); // Tambahkan kolom volume jika diperlukan
            $table->dateTime('measured_at');
        });

        // Menambahkan foreign key constraint setelah tabel dibuat
        Schema::table('flow_pressure_sensors', function (Blueprint $table) {
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flow_pressure_sensors');
    }
};
