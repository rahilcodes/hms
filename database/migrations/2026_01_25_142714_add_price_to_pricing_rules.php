<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('type');
            $table->dropColumn('multiplier');
        });
    }

    public function down(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            $table->decimal('multiplier', 5, 2)->nullable();
            $table->dropColumn('price');
        });
    }
};
