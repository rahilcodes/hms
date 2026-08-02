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
        Schema::table('lost_found_items', function (Blueprint $table) {
            $table->dropForeign(['found_by_user_id']);
            $table->foreign('found_by_user_id')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_found_items', function (Blueprint $table) {
            $table->dropForeign(['found_by_user_id']);
            $table->foreign('found_by_user_id')->references('id')->on('users');
        });
    }
};
