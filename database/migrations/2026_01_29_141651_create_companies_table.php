<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->string('email')->nullable();
            $col->string('phone')->nullable();
            $col->string('gst_number')->nullable();
            $col->text('address')->nullable();
            $col->decimal('credit_limit', 15, 2)->default(0);
            $col->boolean('is_active')->default(true);
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
