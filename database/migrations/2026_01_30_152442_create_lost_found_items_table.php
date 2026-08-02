<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lost_found_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->default(1);
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('found_by_user_id')->constrained('users');
            $table->foreignId('guest_id')->nullable(); // Helper to link to booking_guests if known

            $table->string('item_name');
            $table->text('description')->nullable();
            $table->enum('category', ['electronics', 'clothing', 'documents', 'valuables', 'others'])->default('others');

            $table->string('found_location');
            $table->dateTime('found_date');

            $table->enum('status', ['found', 'claimed', 'disposed', 'donated'])->default('found');

            $table->string('claimed_by_name')->nullable();
            $table->dateTime('claimed_date')->nullable();

            $table->string('image_path')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_found_items');
    }
};
