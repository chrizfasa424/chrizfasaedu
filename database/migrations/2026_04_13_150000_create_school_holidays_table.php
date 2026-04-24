<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->string('name');
            $table->date('holiday_date');
            $table->boolean('is_public')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'holiday_date']);
            $table->unique(['school_id', 'holiday_date', 'session_id', 'term_id'], 'school_holidays_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_holidays');
    }
};

