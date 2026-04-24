<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('result_batches', function (Blueprint $table) {
            $table->string('assessment_type', 40)->default('full_result')->after('exam_type_id');
            $table->index(['assessment_type'], 'result_batches_assessment_type_idx');
        });

        DB::table('result_batches')
            ->whereNull('assessment_type')
            ->update(['assessment_type' => 'full_result']);
    }

    public function down(): void
    {
        Schema::table('result_batches', function (Blueprint $table) {
            $table->dropIndex('result_batches_assessment_type_idx');
            $table->dropColumn('assessment_type');
        });
    }
};

