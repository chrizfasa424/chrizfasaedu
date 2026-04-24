<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bursary_signatures', function (Blueprint $table) {
            $table->string('signature_role', 40)->default('bursar')->after('title');
            $table->index(['school_id', 'signature_role', 'is_default'], 'bursary_signatures_role_default_idx');
        });

        DB::table('bursary_signatures')
            ->whereNull('signature_role')
            ->orWhere('signature_role', '')
            ->update(['signature_role' => 'bursar']);
    }

    public function down(): void
    {
        Schema::table('bursary_signatures', function (Blueprint $table) {
            $table->dropIndex('bursary_signatures_role_default_idx');
            $table->dropColumn('signature_role');
        });
    }
};
