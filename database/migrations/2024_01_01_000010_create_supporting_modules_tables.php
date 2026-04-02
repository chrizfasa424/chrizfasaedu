<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Communication ──────────────────────────────────
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->string('type')->default('general');
            $table->string('audience')->default('all');
            $table->unsignedBigInteger('target_class_id')->nullable();
            $table->string('priority')->default('normal');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_email')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('target_class_id')->references('id')->on('classes')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_type')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->string('channel')->default('in_app');
            $table->string('status')->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('sms_reference')->nullable();
            $table->string('email_reference')->nullable();
            $table->timestamps();
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('recipient_id')->references('id')->on('users')->nullOnDelete();
        });

        // ── Library ────────────────────────────────────────
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->string('category')->nullable();
            $table->string('edition')->nullable();
            $table->year('publication_year')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('shelf_number')->nullable();
            $table->string('rack_number')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('book_borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->unsignedBigInteger('borrower_id');
            $table->string('borrower_type');
            $table->date('borrowed_date');
            $table->date('due_date');
            $table->date('returned_date')->nullable();
            $table->string('status')->default('borrowed');
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->timestamps();
            $table->foreign('issued_by')->references('id')->on('users')->nullOnDelete();
        });

        // ── Transport ──────────────────────────────────────
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('pickup_points')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->integer('capacity')->default(30);
            $table->decimal('fee_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('driver_id')->references('id')->on('staff')->nullOnDelete();
        });

        // ── Hostel ─────────────────────────────────────────
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['male', 'female']);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('warden_id')->nullable();
            $table->integer('capacity')->default(100);
            $table->decimal('fee_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('warden_id')->references('id')->on('staff')->nullOnDelete();
        });

        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->string('room_number');
            $table->integer('capacity')->default(4);
            $table->integer('occupied')->default(0);
            $table->string('floor')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['hostel_id', 'room_number']);
        });

        // Add hostel/transport FKs to students
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('hostel_room_id')->references('id')->on('hostel_rooms')->nullOnDelete();
            $table->foreign('transport_route_id')->references('id')->on('transport_routes')->nullOnDelete();
        });

        // ── Health ─────────────────────────────────────────
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->json('medications')->nullable();
            $table->string('doctor_name')->nullable();
            $table->date('visit_date');
            $table->date('follow_up_date')->nullable();
            $table->json('attachments')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
        });

        // ── Inventory / Assets ─────────────────────────────
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('asset_code')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->string('condition')->default('good');
            $table->date('warranty_expires')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('vendor')->nullable();
            $table->date('maintenance_date');
            $table->date('next_due_date')->nullable();
            $table->string('status')->default('completed');
            $table->string('performed_by')->nullable();
            $table->timestamps();
        });

        // ── CMS ────────────────────────────────────────────
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->string('type')->default('page');
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->timestamp('event_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['school_id', 'slug']);
        });

        // ── Audit Logs ─────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->index(['school_id', 'user_id']);
            $table->index(['model_type', 'model_id']);
        });

        // ── Subscriptions (SaaS) ───────────────────────────
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name');
            $table->string('plan_code');
            $table->decimal('amount', 15, 2);
            $table->string('billing_cycle')->default('yearly');
            $table->integer('student_limit')->default(500);
            $table->integer('staff_limit')->default(50);
            $table->integer('storage_limit_gb')->default(5);
            $table->json('features')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->string('payment_reference')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
        });

        // ── Cache & Jobs (Laravel) ─────────────────────────
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('medical_records');
        Schema::table('students', function (Blueprint $t) {
            $t->dropForeign(['hostel_room_id']);
            $t->dropForeign(['transport_route_id']);
        });
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('book_borrowings');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('announcements');
    }
};
