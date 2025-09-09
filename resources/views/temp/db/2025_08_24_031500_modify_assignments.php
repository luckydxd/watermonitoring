<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Hapus foreign key 'complaint_id' yang lama jika ada
            // Cek ini membuat migrasi lebih aman jika dijalankan ulang
            if (Schema::hasColumn('assignments', 'complaint_id')) {
                // Asumsi nama constraint adalah 'assignments_complaint_id_foreign'
                // Laravel membuatnya secara otomatis dengan format ini.
                $table->dropForeign('assignments_complaint_id_foreign');
                $table->dropColumn('complaint_id');
            }

            // Tambahkan kolom polymorphic 'assignable' setelah kolom 'admin_id'
            // Menggunakan uuidMorphs agar tipe data ID-nya adalah UUID
            $table->uuidMorphs('assignable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Hapus kolom polymorphic
            $table->dropMorphs('assignable');

            // Kembalikan kolom 'complaint_id' yang lama jika migrasi di-rollback
            $table->foreignUuid('complaint_id')->nullable()->constrained('complaints');
        });
    }
};
