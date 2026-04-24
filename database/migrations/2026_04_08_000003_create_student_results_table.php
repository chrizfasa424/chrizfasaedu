<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('result_batch_id')->nullable()->constrained('result_batches')->nullOnDelete();
            $table->string('section')->nullable();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained('exam_types')->cascadeOnDelete();
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->decimal('class_average', 5, 2)->nullable();
            $table->integer('class_position')->nullable();
            $table->unsignedBigInteger('promoted_to_class_id')->nullable();
            $table->unsignedInteger('attendance_present')->default(0);
            $table->unsignedInteger('attendance_total')->default(0);
            $table->text('class_teacher_remark')->nullable();
            $table->text('principal_remark')->nullable();
            $table->string('principal_signature')->nullable();
            $table->date('signed_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('arm_id')->references('id')->on('class_arms')->nullOnDelete();
            $table->foreign('promoted_to_class_id')->references('id')->on('classes')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['student_id', 'session_id', 'term_id', 'exam_type_id'], 'student_results_student_scope_unique');
            $table->index(['school_id', 'class_id', 'arm_id', 'session_id', 'term_id', 'exam_type_id'], 'student_results_sheet_filter_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};

