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
        // 1. Assets (The "DNA" of the hotel)
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->nullable()->constrained()->nullOnDelete(); // Assign to room type, not specific room for now, or room_id if available
            $table->unsignedBigInteger('room_id')->nullable(); // Soft link to room if needed
            $table->string('name'); // e.g. "Samsung AC 1.5T"
            $table->string('type'); // AC, TV, Furniture, Linen, Appliance
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('status')->default('active'); // active, in-repair, retired
            $table->string('qr_code')->unique(); // Unique string for QR generation
            $table->timestamps();
        });

        // 2. Maintenance Schedules (Preventive Rules)
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->cascadeOnDelete(); // Can be null for general room maintenance
            $table->string('title'); // "Clean AC Filters"
            $table->integer('frequency_days'); // e.g. 30
            $table->timestamp('last_performed_at')->nullable();
            $table->timestamp('next_due_at')->nullable();
            $table->timestamps();
        });

        // 3. Maintenance Logs (Repairs & Tasks)
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('technician_name')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, in-progress, completed
            $table->json('photos')->nullable(); // Paths to photos
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 4. Lost & Found
        Schema::create('lost_and_found', function (Blueprint $table) {
            $table->id();
            $table->string('found_location'); // "Room 101" or "Lobby"
            $table->string('description');
            $table->string('category'); // Electronics, Clothing, etc.
            $table->string('found_by')->nullable(); // Staff name
            $table->timestamp('found_at');
            $table->string('status')->default('stored'); // stored, claimed, disposed
            $table->text('guest_details')->nullable(); // If matched to a guest
            $table->timestamps();
        });

        // 5. Laundry Batches - MOVED TO 2026_01_30_132236_create_linen_laundry_tables.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_tables');
    }
};
