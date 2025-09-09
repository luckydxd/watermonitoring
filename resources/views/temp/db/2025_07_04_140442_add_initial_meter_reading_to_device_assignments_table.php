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
        Schema::table('device_assignments', function (Blueprint $table) {
            $table->double('initial_meter_reading', 15, 4)->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_assignments', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback.
            $table->dropColumn('initial_meter_reading');
        });
    }
};
