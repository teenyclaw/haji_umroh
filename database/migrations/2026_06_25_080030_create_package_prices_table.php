<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('room_type');
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['package_id', 'room_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_prices');
    }
};
