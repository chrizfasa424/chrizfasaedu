<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->index('school_id');
            $table->dropUnique(['school_id', 'grade_level']);
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->unique(['school_id', 'grade_level']);
            $table->dropIndex(['school_id']);
        });
    }
};
