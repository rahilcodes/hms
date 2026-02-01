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
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'price_unit')) {
                $table->string('price_unit')->default('fixed')->after('price');
            }
        });

        Schema::table('room_types', function (Blueprint $table) {
            if (!Schema::hasColumn('room_types', 'base_occupancy')) {
                $table->integer('base_occupancy')->default(2)->after('base_price');
            }
            if (!Schema::hasColumn('room_types', 'max_extra_persons')) {
                $table->integer('max_extra_persons')->default(0)->after('base_occupancy');
            }
            if (!Schema::hasColumn('room_types', 'extra_person_price')) {
                $table->decimal('extra_person_price', 10, 2)->default(0)->after('max_extra_persons');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_unit']);
        });

        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['base_occupancy', 'max_extra_persons', 'extra_person_price']);
        });
    }
};
