<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('full_name', 120);
            $table->string('role_title', 140)->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('message');
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'status', 'created_at']);
            $table->index(['school_id', 'status', 'reviewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};

