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
        Schema::create('night_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->date('audit_date')->unique();
            $table->foreignId('performed_by_admin_id')->constrained('admins');
            $table->decimal('revenue_total', 15, 2)->default(0);
            $table->decimal('occupancy_rate', 5, 2)->default(0);
            $table->integer('no_shows_count')->default(0);
            $table->integer('checked_out_count')->default(0);
            $table->enum('status', ['completed', 'failed'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('night_audits');
    }
};
