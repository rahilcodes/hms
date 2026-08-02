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
        Schema::table('bookings', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
            $table->index('uuid');
        });

        // Backfill UUIDs
        $bookings = \Illuminate\Support\Facades\DB::table('bookings')->whereNull('uuid')->get();
        foreach ($bookings as $booking) {
            \Illuminate\Support\Facades\DB::table('bookings')
                ->where('id', $booking->id)
                ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
        }

        // Enforce non-null after backfill
        Schema::table('bookings', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        Schema::table('booking_guests', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->string('nationality')->nullable()->after('email');
            $table->text('address')->nullable()->after('nationality');
            $table->string('purpose_of_visit')->nullable()->after('address');
            $table->string('id_proof_path')->nullable()->after('purpose_of_visit');
            $table->string('signature_path')->nullable()->after('id_proof_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('booking_guests', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'nationality',
                'address',
                'purpose_of_visit',
                'id_proof_path',
                'signature_path'
            ]);
        });
    }
};
