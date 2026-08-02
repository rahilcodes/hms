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
        // 1. Linen Types (Inventory Master)
        Schema::create('linen_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Bath Towel, King Sheet
            $table->enum('category', ['bedding', 'bath', 'fb', 'staff', 'other'])->default('other');
            $table->integer('par_level')->default(10); // Minimum required stock
            $table->integer('total_stock')->default(0); // Current total owned (Clean + Dirty + In Laundry)
            $table->decimal('cost_per_unit', 8, 2)->default(0); // For loss calculation
            $table->timestamps();
        });

        // 2. Laundry Vendors
        Schema::create('laundry_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->json('rate_card_json')->nullable(); // Cost per item type
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Laundry Batches (Events)
        Schema::create('laundry_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique(); // e.g., LND-2024-001
            $table->foreignId('vendor_id')->constrained('laundry_vendors')->onDelete('cascade');
            $table->enum('status', ['out', 'processing', 'returned', 'completed'])->default('out');
            $table->date('sent_date');
            $table->date('expected_return_date')->nullable();
            $table->date('received_date')->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Laundry Items (Pivot/Detail)
        Schema::create('laundry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('laundry_batches')->onDelete('cascade');
            $table->foreignId('linen_type_id')->constrained('linen_types')->onDelete('cascade');
            $table->integer('quantity_sent')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->integer('quantity_rejected')->default(0); // Stained/Torn
            $table->decimal('cost_incurred', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_items');
        Schema::dropIfExists('laundry_batches');
        Schema::dropIfExists('laundry_vendors');
        Schema::dropIfExists('linen_types');
    }
};
