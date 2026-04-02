<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('name');
            $table->string('category'); // tuition, development_levy, ict, uniform, exam, pta, transport, hostel
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_compulsory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('due_date')->nullable();
            $table->decimal('late_fee_amount', 15, 2)->default(0);
            $table->integer('late_fee_after_days')->default(30);
            $table->timestamps();

            $table->foreign('term_id')->references('id')->on('academic_terms')->nullOnDelete();
            $table->foreign('class_id')->references('id')->on('classes')->nullOnDelete();
        });

        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 15, 2);
            $table->json('criteria')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('scholarship_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('scholarship_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->date('due_date')->nullable();
            $table->boolean('late_fee_applied')->default(false);
            $table->decimal('late_fee_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();

            $table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'student_id', 'status']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('fee_structure_id')->nullable();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->timestamps();

            $table->foreign('fee_structure_id')->references('id')->on('fee_structures')->nullOnDelete();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('payment_reference')->unique();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // paystack, flutterwave, bank_transfer, cash, pos
            $table->string('payment_gateway')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'status']);
        });

        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('basic_salary', 15, 2);
            $table->json('allowances')->nullable();
            $table->json('deductions')->nullable();
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->string('payment_method')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['staff_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('scholarship_student');
        Schema::dropIfExists('scholarships');
        Schema::dropIfExists('fee_structures');
    }
};
