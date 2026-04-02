<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('Nigeria');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('motto')->nullable();
            $table->string('website')->nullable();
            $table->year('established_year')->nullable();
            $table->string('registration_number')->nullable();
            $table->enum('school_type', ['primary', 'secondary', 'combined'])->default('combined');
            $table->enum('ownership', ['private', 'government', 'mission'])->default('private');
            $table->string('subscription_plan')->default('basic');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
