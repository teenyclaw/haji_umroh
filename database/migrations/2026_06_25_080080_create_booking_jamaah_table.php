<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_jamaah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jamaah_id')->constrained('jamaah')->cascadeOnDelete();
            $table->foreignId('rombongan_id')->nullable()->constrained('rombongan')->nullOnDelete();
            $table->string('room_type')->default('quad');
            $table->decimal('price', 15, 2)->default(0);
            $table->string('seat_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_jamaah');
    }
};
