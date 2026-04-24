<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->longText('submission_text')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type', 20)->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->longText('teacher_feedback')->nullable();
            $table->longText('student_feedback')->nullable();
            $table->timestamp('student_feedback_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('status', 30)->default('submitted');
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['assignment_id', 'student_id'], 'assignment_submissions_assignment_student_unique');
            $table->index(['school_id', 'status'], 'assignment_submissions_school_status_idx');
            $table->index(['assignment_id', 'submitted_at'], 'assignment_submissions_assignment_submitted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
