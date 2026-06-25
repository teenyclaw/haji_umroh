<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('type')->default('umroh');
            $table->text('description')->nullable();
            $table->unsignedInteger('quota')->default(0);
            $table->unsignedInteger('duration_days')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('airline')->nullable();
            $table->string('departure_airport')->nullable();
            $table->string('hotel_makkah')->nullable();
            $table->unsignedTinyInteger('hotel_makkah_star')->nullable();
            $table->string('hotel_makkah_distance')->nullable();
            $table->string('hotel_madinah')->nullable();
            $table->unsignedTinyInteger('hotel_madinah_star')->nullable();
            $table->string('hotel_madinah_distance')->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('handling_fee', 15, 2)->default(0);
            $table->decimal('insurance_fee', 15, 2)->default(0);
            $table->decimal('visa_fee', 15, 2)->default(0);
            $table->string('ppiu_number')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
