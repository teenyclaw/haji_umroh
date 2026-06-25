<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jamaah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();

            $table->string('nik', 16)->nullable()->index();
            $table->string('full_name');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 1)->default('L');
            $table->string('marital_status')->nullable();

            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();

            $table->string('passport_number')->nullable();
            $table->date('passport_issued_at')->nullable();
            $table->date('passport_expired_at')->nullable();
            $table->string('passport_office')->nullable();

            $table->string('mahram_relation')->nullable();
            $table->string('mahram_name')->nullable();
            $table->foreignId('mahram_jamaah_id')->nullable();

            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('shirt_size')->nullable();
            $table->text('health_notes')->nullable();
            $table->string('porsi_number')->nullable()->comment('Nomor porsi haji');

            $table->string('status')->default('prospek')->index();
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah');
    }
};
