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
        // SQLite doesn't support easy column modification for enums, but we can try 
        // Or just allow it to be a string if we prefer. 
        // Since we just created it, we can safely recreate if needed, but let's try standard first.
        Schema::table('folio_items', function (Blueprint $table) {
            $table->string('type')->change(); // Transition to string for more flexibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folio_items', function (Blueprint $table) {
            $table->enum('type', ['room_rent', 'tax', 'service', 'discount', 'adjustment', 'payment_refund'])->change();
        });
    }
};
