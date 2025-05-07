<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('buyer_name')->nullable()->after('method');
            $table->string('buyer_email')->nullable()->after('buyer_name');
            $table->string('buyer_phone')->nullable()->after('buyer_email');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'buyer_name',
                'buyer_email',
                'buyer_phone',
            ]);
        });
    }
};
