<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->timestamp('first_test_imported_at')->nullable()->after('updated_by');
            $table->timestamp('second_test_imported_at')->nullable()->after('first_test_imported_at');
            $table->timestamp('exam_imported_at')->nullable()->after('second_test_imported_at');
            $table->timestamp('full_result_imported_at')->nullable()->after('exam_imported_at');

            $table->timestamp('first_test_published_at')->nullable()->after('published_at');
            $table->unsignedBigInteger('first_test_published_by')->nullable()->after('first_test_published_at');
            $table->timestamp('second_test_published_at')->nullable()->after('first_test_published_by');
            $table->unsignedBigInteger('second_test_published_by')->nullable()->after('second_test_published_at');
            $table->timestamp('exam_published_at')->nullable()->after('second_test_published_by');
            $table->unsignedBigInteger('exam_published_by')->nullable()->after('exam_published_at');
            $table->timestamp('full_result_published_at')->nullable()->after('exam_published_by');
            $table->unsignedBigInteger('full_result_published_by')->nullable()->after('full_result_published_at');

            $table->foreign('first_test_published_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('second_test_published_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('exam_published_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('full_result_published_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->dropForeign(['first_test_published_by']);
            $table->dropForeign(['second_test_published_by']);
            $table->dropForeign(['exam_published_by']);
            $table->dropForeign(['full_result_published_by']);

            $table->dropColumn([
                'first_test_imported_at',
                'second_test_imported_at',
                'exam_imported_at',
                'full_result_imported_at',
                'first_test_published_at',
                'first_test_published_by',
                'second_test_published_at',
                'second_test_published_by',
                'exam_published_at',
                'exam_published_by',
                'full_result_published_at',
                'full_result_published_by',
            ]);
        });
    }
};

