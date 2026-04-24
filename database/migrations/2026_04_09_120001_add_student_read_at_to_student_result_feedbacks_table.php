<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_result_feedbacks', function (Blueprint $table) {
            if (!Schema::hasColumn('student_result_feedbacks', 'student_read_at')) {
                $table->timestamp('student_read_at')->nullable()->after('responded_at');
                $table->index(['student_id', 'student_read_at'], 'student_result_feedbacks_student_read_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_result_feedbacks', function (Blueprint $table) {
            if (Schema::hasColumn('student_result_feedbacks', 'student_read_at')) {
                $table->dropIndex('student_result_feedbacks_student_read_idx');
                $table->dropColumn('student_read_at');
            }
        });
    }
};

