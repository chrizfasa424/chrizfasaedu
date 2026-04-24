<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id');
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('attachment_path');
            $table->string('attachment_type', 20);
            $table->string('status', 30)->default('draft');
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('published_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['school_id', 'status'], 'assignments_school_status_idx');
            $table->index(['subject_id', 'session_id', 'term_id'], 'assignments_scope_idx');
        });

        Schema::create('assignment_class_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->timestamps();

            $table->foreign('arm_id')->references('id')->on('class_arms')->nullOnDelete();
            $table->unique(['assignment_id', 'class_id', 'arm_id'], 'assignment_class_targets_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_class_targets');
        Schema::dropIfExists('assignments');
    }
};
