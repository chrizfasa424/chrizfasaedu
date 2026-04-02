<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('photo')->nullable();
            $table->string('relationship_type')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('admission_id')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->unsignedBigInteger('arm_id')->nullable();
            $table->string('admission_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_names')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->string('nationality')->default('Nigerian');
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('photo')->nullable();
            $table->string('previous_school')->nullable();
            $table->json('medical_conditions')->nullable();
            $table->json('allergies')->nullable();
            $table->text('disabilities')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('session_admitted')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_boarding')->default(false);
            $table->unsignedBigInteger('hostel_room_id')->nullable();
            $table->unsignedBigInteger('transport_route_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_id', 'admission_number']);
            $table->index(['school_id', 'class_id', 'status']);
            $table->foreign('arm_id')->references('id')->on('class_arms')->nullOnDelete();
        });

        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('relationship')->default('parent');
            $table->timestamps();
            $table->unique(['parent_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('students');
        Schema::dropIfExists('parents');
    }
};
