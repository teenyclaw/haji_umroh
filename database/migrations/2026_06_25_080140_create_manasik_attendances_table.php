<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manasik_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manasik_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jamaah_id')->constrained('jamaah')->cascadeOnDelete();
            $table->boolean('present')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['manasik_schedule_id', 'jamaah_id'], 'manasik_att_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manasik_attendances');
    }
};
