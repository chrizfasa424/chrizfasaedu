<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('type'); // ca1, ca2, ca3, exam, project, assignment
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('staff')->nullOnDelete();
            $table->index(['student_id', 'subject_id', 'session_id', 'term_id']);
        });

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->decimal('ca1_score', 5, 2)->default(0);
            $table->decimal('ca2_score', 5, 2)->default(0);
            $table->decimal('ca3_score', 5, 2)->default(0);
            $table->decimal('exam_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->string('grade')->nullable();
            $table->decimal('grade_point', 3, 2)->default(0);
            $table->integer('position_in_subject')->nullable();
            $table->text('teacher_remark')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['student_id', 'subject_id', 'session_id', 'term_id']);
        });

        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->integer('position_in_class')->nullable();
            $table->integer('class_size')->default(0);
            $table->integer('total_subjects')->default(0);
            $table->integer('subjects_passed')->default(0);
            $table->integer('subjects_failed')->default(0);
            $table->text('class_teacher_remark')->nullable();
            $table->text('principal_remark')->nullable();
            $table->date('next_term_begins')->nullable();
            $table->decimal('next_term_fees', 15, 2)->nullable();
            $table->integer('attendance_present')->default(0);
            $table->integer('attendance_absent')->default(0);
            $table->integer('attendance_total')->default(0);
            $table->boolean('is_published')->default(false);
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'session_id', 'term_id']);
        });

        Schema::create('behaviour_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->enum('type', ['reward', 'sanction', 'observation']);
            $table->string('category')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('action_taken')->nullable();
            $table->enum('severity', ['minor', 'moderate', 'major', 'critical'])->nullable();
            $table->integer('points')->default(0);
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->date('date_of_incident');
            $table->timestamps();

            $table->foreign('reported_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('handled_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('behaviour_records');
        Schema::dropIfExists('report_cards');
        Schema::dropIfExists('results');
        Schema::dropIfExists('assessments');
    }
};
