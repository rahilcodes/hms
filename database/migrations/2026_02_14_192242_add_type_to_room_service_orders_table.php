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
        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->string('type')->default('dining')->after('booking_id'); // dining, service
        });

        // Auto-fix existing records: If 'items' JSON contains "unit" key, it's likely a service (extra bed, etc.)
        // BookingController adds 'unit' to items. GuestDiningController adds 'subtotal'.
        try {
            \DB::statement("UPDATE room_service_orders SET type = 'service' WHERE items LIKE '%\"unit\":%'");
        } catch (\Exception $e) {
            // Ignore if fails
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_service_orders', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
