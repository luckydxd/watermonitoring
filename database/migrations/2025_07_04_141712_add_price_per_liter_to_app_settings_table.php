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
        Schema::table('app_settings', function (Blueprint $table) {
            // Menambahkan kolom baru untuk harga per liter.
            // Tipe 'decimal' sangat direkomendasikan untuk nilai mata uang agar presisi.
            // (8, 2) berarti total 8 digit, dengan 2 digit di belakang koma.
            // ->after() hanya untuk kerapian, menempatkannya setelah kolom 'gmap_coordinat'.
            $table->decimal('price_per_liter', 8, 2)->default(0.00)->after('gmap_coordinat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback.
            $table->dropColumn('price_per_liter');
        });
    }
};
