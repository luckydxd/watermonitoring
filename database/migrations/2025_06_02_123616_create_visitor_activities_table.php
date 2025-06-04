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
        // database/migrations/[timestamp]_create_visitor_activities_table.php
        Schema::create('visitor_activities', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->integer('visitors')->default(0);
            $table->integer('contact_clicks')->default(0);
            $table->integer('login_clicks')->default(0);
            $table->integer('download_clicks')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_activities');
    }
};
