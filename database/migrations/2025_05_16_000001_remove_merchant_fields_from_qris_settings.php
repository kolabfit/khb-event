<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('qris_settings', function (Blueprint $table) {
            $table->dropColumn(['merchant_id', 'merchant_type']);
        });
    }

    public function down(): void
    {
        Schema::table('qris_settings', function (Blueprint $table) {
            $table->string('merchant_id')->after('merchant_city');
            $table->string('merchant_type')->after('merchant_id');
        });
    }
}; 