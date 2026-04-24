<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('section')->nullable();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->foreignId('exam_type_id')->constrained('exam_types')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('stored_path')->nullable();
            $table->string('source_type')->default('upload');
            $table->string('import_mode')->default('create_only');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('success_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->string('status')->default('validating');
            $table->json('summary')->nullable();
            $table->unsignedBigInteger('imported_by');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->foreign('arm_id')->references('id')->on('class_arms')->nullOnDelete();
            $table->foreign('imported_by')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['school_id', 'class_id', 'session_id', 'term_id', 'exam_type_id'], 'result_batches_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_batches');
    }
};

