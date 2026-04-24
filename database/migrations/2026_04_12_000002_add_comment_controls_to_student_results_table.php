<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->text('vice_principal_remark')->nullable()->after('principal_remark');
            $table->boolean('class_teacher_remark_active')->default(true)->after('vice_principal_remark');
            $table->boolean('principal_remark_active')->default(true)->after('class_teacher_remark_active');
            $table->boolean('vice_principal_remark_active')->default(true)->after('principal_remark_active');
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->dropColumn([
                'vice_principal_remark',
                'class_teacher_remark_active',
                'principal_remark_active',
                'vice_principal_remark_active',
            ]);
        });
    }
};

