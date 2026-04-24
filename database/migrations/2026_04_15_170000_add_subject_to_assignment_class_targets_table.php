<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assignment_class_targets', 'subject_id')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->foreignId('subject_id')->nullable()->after('arm_id')->constrained()->nullOnDelete();
            });
        }

        if (!$this->indexExists('assignment_class_targets', 'assignment_class_targets_assignment_idx')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->index('assignment_id', 'assignment_class_targets_assignment_idx');
            });
        }

        if (!$this->indexExists('assignment_class_targets', 'assignment_class_targets_arm_idx')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->index('arm_id', 'assignment_class_targets_arm_idx');
            });
        }

        if ($this->indexExists('assignment_class_targets', 'assignment_class_targets_unique')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->dropUnique('assignment_class_targets_unique');
            });
        }

        if (!$this->indexExists('assignment_class_targets', 'assignment_class_targets_scope_unique')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->unique(
                    ['assignment_id', 'class_id', 'arm_id', 'subject_id'],
                    'assignment_class_targets_scope_unique'
                );
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('assignment_class_targets', 'assignment_class_targets_scope_unique')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->dropUnique('assignment_class_targets_scope_unique');
            });
        }

        if (Schema::hasColumn('assignment_class_targets', 'subject_id')) {
            try {
                Schema::table('assignment_class_targets', function (Blueprint $table) {
                    $table->dropForeign(['subject_id']);
                });
            } catch (\Throwable) {
                // Ignore when foreign key is already absent.
            }

            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->dropColumn('subject_id');
            });
        }

        if (!$this->indexExists('assignment_class_targets', 'assignment_class_targets_unique')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->unique(['assignment_id', 'class_id', 'arm_id'], 'assignment_class_targets_unique');
            });
        }

        if ($this->indexExists('assignment_class_targets', 'assignment_class_targets_assignment_idx')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->dropIndex('assignment_class_targets_assignment_idx');
            });
        }

        if ($this->indexExists('assignment_class_targets', 'assignment_class_targets_arm_idx')) {
            Schema::table('assignment_class_targets', function (Blueprint $table) {
                $table->dropIndex('assignment_class_targets_arm_idx');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $index = trim($index);
        if ($index === '') {
            return false;
        }

        $rows = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$index]);

        return !empty($rows);
    }
};
