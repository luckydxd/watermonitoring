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
        Schema::create('assignments', function (Blueprint $table) {
            // Menggunakan UUID sebagai primary key, sesuai dengan struktur tabel lain Anda
            $table->uuid('id')->primary();

            // Foreign key ke tabel keluhan (complaints)
            $table->foreignUuid('complaint_id')
                ->constrained('complaints')
                ->onDelete('cascade'); // Jika keluhan dihapus, penugasan terkait juga terhapus

            // Foreign key untuk teknisi yang ditugaskan (dari tabel users)
            $table->foreignUuid('technician_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Foreign key untuk admin yang menugaskan (dari tabel users)
            $table->foreignUuid('admin_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Status penugasan
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');

            // Catatan dari admin saat menugaskan
            $table->text('notes')->nullable();

            // Catatan dari teknisi saat menyelesaikan tugas
            $table->text('completion_notes')->nullable();

            // Waktu tugas diselesaikan
            $table->timestamp('completed_at')->nullable();

            // Kolom created_at dan updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
