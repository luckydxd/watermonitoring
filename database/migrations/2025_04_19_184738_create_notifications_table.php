<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('related_complaint_id')->nullable();
            $table->uuid('related_response_id')->nullable();
            $table->string('title');
            $table->text('content')->nullable();
            $table->enum('type', ['complaint_created', 'complaint_responded', 'general']);
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('related_complaint_id')->references('id')->on('complaints')->onDelete('set null');
            $table->foreign('related_response_id')->references('id')->on('complaint_response')->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
