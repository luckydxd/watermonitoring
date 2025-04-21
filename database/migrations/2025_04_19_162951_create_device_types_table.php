<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('devices_type', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g., "ESP32", "ESP8266"
            $table->timestamp('timestamp')->nullable(); // waktu input jika diperlukan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices_type');
    }
};
