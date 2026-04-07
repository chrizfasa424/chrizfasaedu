<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Drop old stub table ────────────────────────────────
        Schema::dropIfExists('messages');

        // ── messages ──────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->enum('audience', ['all_students', 'all_parents', 'all_portal', 'class'])->default('all_students');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes')->nullOnDelete();
            $table->index(['school_id', 'created_at']);
            $table->index('sender_id');
        });

        // ── message_recipients ────────────────────────────────
        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
            $table->index('message_id');
        });

        // ── message_replies ───────────────────────────────────
        Schema::create('message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_by_admin_at')->nullable();
            $table->timestamps();

            $table->index(['message_id', 'created_at']);
            $table->index(['message_id', 'read_by_admin_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_replies');
        Schema::dropIfExists('message_recipients');
        Schema::dropIfExists('messages');
    }
};
