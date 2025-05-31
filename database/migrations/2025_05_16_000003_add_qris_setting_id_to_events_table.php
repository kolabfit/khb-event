<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('qris_setting_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['qris_setting_id']);
            $table->dropColumn('qris_setting_id');
        });
    }
}; 