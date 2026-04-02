<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->string('application_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_names')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('nationality')->default('Nigerian');
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('class_applied_for');
            $table->string('previous_school')->nullable();
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->string('parent_occupation')->nullable();
            $table->string('photo')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->string('previous_result')->nullable();
            $table->json('other_documents')->nullable();
            $table->decimal('screening_score', 5, 2)->nullable();
            $table->timestamp('screening_date')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('admission_number')->nullable();
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'session_id']);
        });

        // Add admission FK to students
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('admission_id')->references('id')->on('admissions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', fn(Blueprint $t) => $t->dropForeign(['admission_id']));
        Schema::dropIfExists('admissions');
    }
};
