<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('device_id')->constrained('devices')->onDelete('cascade');
            $table->dateTime('assignment_date')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index untuk pencarian yang lebih cepat
            $table->index(['user_id', 'device_id']);
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_assignments');
    }
};
