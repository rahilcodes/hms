<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_type')->default('overnight'); // overnight, day_use
            $table->integer('day_use_hours')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('travel_agents')->nullOnDelete();
            $table->decimal('agent_commission', 10, 2)->default(0);
        });

        Schema::table('room_types', function (Blueprint $table) {
            $table->boolean('day_use_enabled')->default(false);
            $table->decimal('day_use_price_4h', 10, 2)->nullable();
            $table->decimal('day_use_price_8h', 10, 2)->nullable();
            $table->decimal('child_price', 10, 2)->default(0);       // per child (5-12 yrs) per night
            $table->decimal('extra_mattress_price', 10, 2)->default(0);
        });

        Schema::table('booking_guests', function (Blueprint $table) {
            $table->string('id_type')->nullable();   // aadhaar, passport, driving_license, voter_id, pan
            $table->string('id_number')->nullable();
            $table->string('visa_number')->nullable();
            $table->date('visa_expiry')->nullable();
            $table->string('arrived_from')->nullable();
            $table->string('next_destination')->nullable();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->json('permissions')->nullable();
        });

        Schema::table('night_audits', function (Blueprint $table) {
            $table->decimal('cash_expected', 12, 2)->nullable();
            $table->decimal('cash_counted', 12, 2)->nullable();
            $table->decimal('cash_variance', 12, 2)->nullable();
        });

        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->integer('kot_number')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('room_service_orders', fn (Blueprint $t) => $t->dropColumn('kot_number'));
        Schema::table('night_audits', fn (Blueprint $t) => $t->dropColumn(['cash_expected', 'cash_counted', 'cash_variance']));
        Schema::table('admins', fn (Blueprint $t) => $t->dropColumn('permissions'));
        Schema::table('booking_guests', fn (Blueprint $t) => $t->dropColumn(['id_type', 'id_number', 'visa_number', 'visa_expiry', 'arrived_from', 'next_destination']));
        Schema::table('room_types', fn (Blueprint $t) => $t->dropColumn(['day_use_enabled', 'day_use_price_4h', 'day_use_price_8h', 'child_price', 'extra_mattress_price']));
        Schema::table('bookings', function (Blueprint $t) {
            $t->dropConstrainedForeignId('agent_id');
            $t->dropColumn(['booking_type', 'day_use_hours', 'agent_commission']);
        });
    }
};
