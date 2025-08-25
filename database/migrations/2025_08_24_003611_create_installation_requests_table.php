<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/..._create_installation_requests_table.php
    public function up(): void
    {
        Schema::create('installation_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('customer_name');
            $table->text('customer_address');
            $table->string('customer_phone');

            // ID user jika calon pelanggan sudah punya akun (bisa null jika pendaftar baru)
            $table->foreignUuid('user_id')->nullable()->constrained('users');

            // Cabang yang akan menangani
            $table->foreignUuid('branch_id')->constrained('branches');

            // Status permintaan
            $table->enum('status', ['pending_review', 'approved', 'scheduled', 'completed', 'rejected'])->default('pending_review');

            $table->text('survey_notes')->nullable(); // Catatan dari tim survei
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_requests');
    }
};
