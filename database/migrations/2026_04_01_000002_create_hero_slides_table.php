<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('subtitle');
            $table->string('badge_text');
            $table->string('button_1_text');
            $table->string('button_1_link');
            $table->string('button_2_text');
            $table->string('button_2_link');
            $table->string('right_card_title');
            $table->text('right_card_text');
            $table->string('school_name');
            $table->string('image_path');
            $table->unsignedInteger('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['school_id', 'is_active', 'order']);
            $table->index(['school_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
