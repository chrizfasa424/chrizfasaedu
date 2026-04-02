<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            $table->unique(['school_id', 'slug']);
        });

        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->string('name');
            $table->string('term');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->unique(['session_id', 'term']);
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('grade_level');
            $table->string('section')->nullable();
            $table->integer('capacity')->default(40);
            $table->unsignedBigInteger('class_teacher_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['school_id', 'grade_level']);
        });

        Schema::create('class_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('name');
            $table->integer('capacity')->default(40);
            $table->unsignedBigInteger('arm_teacher_id')->nullable();
            $table->timestamps();
            $table->unique(['class_id', 'name']);
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('department')->nullable();
            $table->boolean('is_compulsory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('credit_unit')->default(1);
            $table->timestamps();
            $table->unique(['school_id', 'code']);
        });

        Schema::create('class_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->timestamps();
            $table->unique(['class_id', 'subject_id']);
        });

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('class_subject');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('class_arms');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('academic_sessions');
    }
};
