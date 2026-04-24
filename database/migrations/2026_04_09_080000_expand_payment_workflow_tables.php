<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->json('settings_json')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_method_id')) {
                $table->unsignedBigInteger('payment_method_id')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('payments', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('payments', 'gateway_name')) {
                $table->string('gateway_name')->nullable()->after('payment_gateway');
            }
            if (!Schema::hasColumn('payments', 'amount_expected')) {
                $table->decimal('amount_expected', 15, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('amount_expected');
            }
            if (!Schema::hasColumn('payments', 'proof_file_path')) {
                $table->string('proof_file_path')->nullable()->after('payment_date');
            }
            if (!Schema::hasColumn('payments', 'proof_original_name')) {
                $table->string('proof_original_name')->nullable()->after('proof_file_path');
            }
            if (!Schema::hasColumn('payments', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('payments', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('submitted_by');
            }
            if (!Schema::hasColumn('payments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('payments', 'verification_note')) {
                $table->text('verification_note')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('payments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verification_note');
            }
            if (!Schema::hasColumn('payments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('payments', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('payments', 'meta_json')) {
                $table->json('meta_json')->nullable()->after('gateway_response');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'submitted_by')) {
                $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
            }
            if (Schema::hasColumn('payments', 'verified_by')) {
                $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            }
            if (Schema::hasColumn('payments', 'payment_method_id')) {
                $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();
            }
        });

        Schema::create('school_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('branch')->nullable();
            $table->text('instruction_note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('bursary_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('signature_path');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number')->unique();
            $table->string('pdf_path')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();

            $table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['school_id', 'payment_id']);
        });

        DB::table('payment_methods')->insert([
            [
                'school_id' => null,
                'code' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'is_active' => true,
                'settings_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => null,
                'code' => 'pos',
                'name' => 'POS Payment',
                'is_active' => true,
                'settings_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => null,
                'code' => 'cash',
                'name' => 'Cash Payment',
                'is_active' => true,
                'settings_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => null,
                'code' => 'flutterwave',
                'name' => 'Flutterwave',
                'is_active' => true,
                'settings_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => null,
                'code' => 'paystack',
                'name' => 'Paystack',
                'is_active' => true,
                'settings_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'submitted_by')) {
                $table->dropForeign(['submitted_by']);
            }
            if (Schema::hasColumn('payments', 'verified_by')) {
                $table->dropForeign(['verified_by']);
            }
            if (Schema::hasColumn('payments', 'payment_method_id')) {
                $table->dropForeign(['payment_method_id']);
            }
        });

        Schema::dropIfExists('receipts');
        Schema::dropIfExists('bursary_signatures');
        Schema::dropIfExists('school_bank_accounts');
        Schema::dropIfExists('payment_methods');

        Schema::table('payments', function (Blueprint $table) {
            $columns = [
                'payment_method_id',
                'gateway_reference',
                'gateway_name',
                'amount_expected',
                'payment_date',
                'proof_file_path',
                'proof_original_name',
                'submitted_by',
                'verified_by',
                'verified_at',
                'verification_note',
                'rejection_reason',
                'approved_at',
                'cancelled_at',
                'meta_json',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
