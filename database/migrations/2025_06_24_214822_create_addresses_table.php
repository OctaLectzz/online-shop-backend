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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name', 50);
            $table->string('phone_number', 15);
            $table->unsignedInteger('province_id');
            $table->string('province_name');
            $table->unsignedInteger('city_id');
            $table->string('city_name');
            $table->unsignedInteger('district_id')->nullable();
            $table->string('district_name')->nullable();
            $table->unsignedInteger('village_id')->nullable();
            $table->string('village_name')->nullable();
            $table->string('postal_code', 10);
            $table->text('address');
            $table->enum('label', ['house', 'office', 'etc']);
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
