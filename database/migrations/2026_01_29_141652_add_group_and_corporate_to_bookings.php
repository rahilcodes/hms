<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $col) {
            $col->string('group_id')->nullable()->after('hotel_id')->index();
            $col->foreignId('company_id')->nullable()->after('group_id')->constrained('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $col) {
            $col->dropColumn(['group_id', 'company_id']);
        });
    }
};
