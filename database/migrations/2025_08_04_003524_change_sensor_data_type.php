<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Untuk FlowPressureSensor
        Schema::table('flow_pressure_sensors', function (Blueprint $table) {
            // Ubah volume ke DECIMAL yang lebih besar atau BIGINT
            $table->decimal('volume', 15, 2)->change(); // 15 digit total, 2 decimal
            // Atau gunakan:
            // $table->bigInteger('volume')->change(); // Jika tidak perlu decimal
        });

        // Untuk WaterConsumptionLog (jika ada kolom total_consumption)
        Schema::table('water_consumption_logs', function (Blueprint $table) {
            $table->decimal('total_consumption', 15, 2)->change();
            // Atau:
            // $table->bigInteger('total_consumption')->change();
        });
    }

    public function down()
    {
        Schema::table('flow_pressure_sensors', function (Blueprint $table) {
            $table->double('volume', 10, 2)->change();
        });

        Schema::table('water_consumption_logs', function (Blueprint $table) {
            $table->double('total_consumption', 10, 2)->change();
        });
    }
};
