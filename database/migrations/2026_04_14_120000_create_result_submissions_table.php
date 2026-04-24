<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id');
            $table->string('section')->nullable();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained('exam_types')->cascadeOnDelete();
            $table->string('assessment_type', 40)->default('full_result');
            $table->string('import_mode', 40)->default('create_only');
            $table->string('file_path');
            $table->string('original_file_name');
            $table->string('status', 40)->default('draft');
            $table->text('staff_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->json('validation_summary')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->unsignedBigInteger('imported_by')->nullable();
            $table->unsignedBigInteger('result_batch_id')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('arm_id')->references('id')->on('class_arms')->nullOnDelete();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('imported_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('result_batch_id')->references('id')->on('result_batches')->nullOnDelete();

            $table->index(['school_id', 'status'], 'result_submissions_school_status_idx');
            $table->index(['class_id', 'session_id', 'term_id', 'exam_type_id'], 'result_submissions_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_submissions');
    }
};
