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
        Schema::create('feature_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('tag', 50)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('button_url')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_blocks');
    }
};
