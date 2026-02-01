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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name'); // e.g., 'Pro', 'Enterprise'
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly');
            $table->date('starts_at');
            $table->date('next_billing_date')->nullable();
            $table->enum('status', ['active', 'past_due', 'cancelled', 'expired'])->default('active');
            $table->json('features_snapshot')->nullable(); // Freeze features at time of sub
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
