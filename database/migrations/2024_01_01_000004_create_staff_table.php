<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('staff_id_number')->nullable();
            $table->string('employee_type')->default('full_time'); // full_time, part_time, contract
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('qualification')->nullable();
            $table->date('date_of_employment')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('marital_status')->nullable();
            $table->string('nationality')->default('Nigerian');
            $table->string('state_of_origin')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->json('allowances')->nullable();
            $table->json('deductions')->nullable();
            $table->string('photo')->nullable();
            $table->string('resume')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['school_id', 'staff_id_number']);
        });

        // Add foreign keys to classes after staff table exists
        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('class_teacher_id')->references('id')->on('staff')->nullOnDelete();
        });
        Schema::table('class_arms', function (Blueprint $table) {
            $table->foreign('arm_teacher_id')->references('id')->on('staff')->nullOnDelete();
        });
        Schema::table('class_subject', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('staff')->nullOnDelete();
        });
        Schema::table('timetables', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('staff')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('timetables', fn(Blueprint $t) => $t->dropForeign(['teacher_id']));
        Schema::table('class_subject', fn(Blueprint $t) => $t->dropForeign(['teacher_id']));
        Schema::table('class_arms', fn(Blueprint $t) => $t->dropForeign(['arm_teacher_id']));
        Schema::table('classes', fn(Blueprint $t) => $t->dropForeign(['class_teacher_id']));
        Schema::dropIfExists('staff');
    }
};
