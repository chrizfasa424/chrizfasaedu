<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_import_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_batch_id')->constrained('result_batches')->cascadeOnDelete();
            $table->unsignedInteger('row_number')->nullable();
            $table->string('column_name')->nullable();
            $table->text('error_message');
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['result_batch_id', 'row_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_import_errors');
    }
};

