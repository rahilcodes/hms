<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('agency_name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('gst_number')->nullable();
            $table->decimal('commission_percent', 5, 2)->default(10);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('banquet_halls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('capacity')->default(100);
            $table->decimal('base_rent', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('banquet_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('banquet_hall_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('customer_gstin')->nullable();
            $table->string('event_type')->default('wedding'); // wedding, reception, conference, birthday, other
            $table->date('event_date');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->integer('guests_expected')->default(0);
            $table->decimal('per_plate_rate', 10, 2)->default(0);
            $table->integer('food_plates')->default(0);
            $table->decimal('hall_rent', 10, 2)->default(0);
            $table->decimal('decoration_charge', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('advance_paid', 10, 2)->default(0);
            $table->string('status')->default('enquiry'); // enquiry, confirmed, completed, cancelled
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['event_date', 'status']);
        });

        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->date('end_date')->nullable();
            $table->integer('suggested_uplift_percent')->default(20);
            $table->string('region')->nullable();
            $table->timestamps();
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festivals');
        Schema::dropIfExists('banquet_bookings');
        Schema::dropIfExists('banquet_halls');
        Schema::dropIfExists('travel_agents');
    }
};
