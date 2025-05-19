<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('ticket_type_id')
                ->nullable()
                ->constrained('ticket_types')
                ->onDelete('set null');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamp('purchased_at')->useCurrent();
            $table->decimal('price_paid', 12, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'used'])
                ->default('pending');
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
