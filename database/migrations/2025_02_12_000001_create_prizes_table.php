<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name (e.g. "Free consultation")
            $table->string('code', 50)->unique(); // Internal code (backend only)
            $table->unsignedInteger('probability_weight')->default(1); // Higher = more likely
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->string('color', 20)->nullable(); // For wheel segment
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
