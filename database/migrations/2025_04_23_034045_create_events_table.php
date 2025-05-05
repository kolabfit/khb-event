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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('location');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->integer('quota');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])
                ->default('draft');
            $table->timestamps();
            $table->string('thumbnail', 255);
            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
