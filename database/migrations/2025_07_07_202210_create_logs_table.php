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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // example: create, update, delete
            $table->text('description')->nullable();
            $table->string('reference_type'); // example: product, order, promo
            $table->unsignedBigInteger('reference_id');
            $table->timestamps();
        });

        Schema::create('log_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_reads');
        Schema::dropIfExists('logs');
    }
};
