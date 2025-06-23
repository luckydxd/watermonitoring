<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('unique_key', 64)->unique()->nullable()->after('unique_id'); // 64 karakter cukup untuk SHA256 hash
            $table->timestamp('last_seen_at')->nullable()->after('status'); // Kapan terakhir perangkat online
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('unique_key');
            $table->dropColumn('last_seen_at');
        });
    }
};
