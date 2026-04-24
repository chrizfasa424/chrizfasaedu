<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_result_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_result_id')->constrained('student_results')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->decimal('exam_score', 5, 2)->default(0);
            $table->decimal('first_test_score', 5, 2)->default(0);
            $table->decimal('second_test_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->integer('subject_position')->nullable();
            $table->string('grade', 20)->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();

            $table->unique(['student_result_id', 'subject_id'], 'student_result_items_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_result_items');
    }
};

