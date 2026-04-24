<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_result_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_result_id')->nullable()->constrained('student_results')->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->foreignId('exam_type_id')->nullable()->constrained('exam_types')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->enum('feedback_type', ['feedback', 'query'])->default('feedback');
            $table->string('title', 180);
            $table->text('message');
            $table->enum('status', ['open', 'in_review', 'resolved', 'closed'])->default('open');
            $table->text('admin_response')->nullable();
            $table->unsignedBigInteger('responded_by')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->foreign('responded_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'student_id', 'feedback_type', 'status'], 'student_result_feedbacks_scope_idx');
            $table->index(['term_id', 'exam_type_id'], 'student_result_feedbacks_term_exam_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_result_feedbacks');
    }
};

