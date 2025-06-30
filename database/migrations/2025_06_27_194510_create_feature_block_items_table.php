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
        Schema::create('feature_block_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_block_id')->constrained()->onDelete('cascade');
            $table->string('icon_class', 100)->nullable();
            $table->string('text');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_block_items');
    }
};
