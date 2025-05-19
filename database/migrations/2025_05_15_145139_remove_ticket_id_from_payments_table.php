<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Hapus constraint foreign key dulu (jika ada)
            $table->dropForeign(['ticket_id']);
            // Lalu drop kolom
            $table->dropColumn('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Kalau rollback, kembalikan kolom ticket_id
            $table->unsignedBigInteger('ticket_id')->after('id');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        });
    }
};
