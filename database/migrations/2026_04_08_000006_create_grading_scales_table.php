<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('section')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('grade', 20);
            $table->decimal('point', 5, 2)->nullable();
            $table->string('remark')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'class_id', 'section', 'is_active'], 'grading_scales_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_scales');
    }
};

