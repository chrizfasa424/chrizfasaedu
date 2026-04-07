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
        Schema::table('admissions', function (Blueprint $table) {
            $table->string('blood_group', 10)->nullable()->after('gender');
            $table->string('genotype', 10)->nullable()->after('blood_group');
            $table->string('religion', 50)->nullable()->after('genotype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn(['blood_group', 'genotype', 'religion']);
        });
    }
};
