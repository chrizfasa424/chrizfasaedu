<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->decimal('class_average', 8, 2)->nullable()->after('average_score');
            $table->decimal('total_ca1', 8, 2)->nullable()->after('class_average');
            $table->decimal('total_ca2', 8, 2)->nullable()->after('total_ca1');
            $table->decimal('total_exam', 8, 2)->nullable()->after('total_ca2');
        });
    }

    public function down(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->dropColumn(['class_average', 'total_ca1', 'total_ca2', 'total_exam']);
        });
    }
};
