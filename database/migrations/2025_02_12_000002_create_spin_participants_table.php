<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spin_participants', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->unique(); // E.164 format
            $table->string('country_code', 5);
            $table->boolean('is_verified')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_participants');
    }
};
