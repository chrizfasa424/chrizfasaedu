<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Academic;
use App\Http\Controllers\Admission\AdmissionController;
use App\Http\Controllers\Financial;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Examination\ResultController;
use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Library\LibraryController;
use App\Http\Controllers\Portal;
use App\Http\Controllers\Transport\TransportController;
use App\Http\Controllers\Hostel\HostelController;
use App\Http\Controllers\Health\MedicalController;
use App\Http\Controllers\Inventory\AssetController;
use App\Http\Controllers\System\SettingsController;
use App\Http\Controllers\System\HeroSlideController;
use App\Http\Controllers\System\TestimonialController;
use App\Http\Controllers\Reporting\ReportController;
use App\Http\Controllers\MultiSchool\TenantController;
use App\Http\Controllers\PublicPageController;

/*
|--------------------------------------------------------------------------
| Web Routes - Enterprise Education Management System
|--------------------------------------------------------------------------
*/

// ── Public / Auth ──────────────────────────────────────────
Route::get('/', [PublicPageController::class, 'index'])->name('public.home');
Route::get('/contact', [PublicPageController::class, 'contactPage'])->name('public.contact');
Route::post('/contact', [PublicPageController::class, 'submitContact'])->name('public.contact.submit');
Route::get('/menu/{section}/{slug}', [PublicPageController::class, 'submenuPage'])->name('public.submenu');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Online Admission (public)
Route::get('/apply', [AdmissionController::class, 'applyOnline'])->name('admission.apply');
Route::post('/apply', [AdmissionController::class, 'store'])->name('admission.apply.store');

// ── Authenticated Routes ───────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/teacher/dashboard', [DashboardController::class, 'teacherDashboard'])->name('teacher.dashboard');
    Route::get('/student/dashboard', [Portal\StudentPortalController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/student/testimonials', [Portal\StudentPortalController::class, 'submitTestimonial'])
        ->middleware('throttle:3,10')
        ->name('student.testimonials.submit');
    Route::get('/parent/dashboard', [Portal\ParentPortalController::class, 'dashboard'])->name('parent.dashboard');

    // ── Academic Module ────────────────────────────────────
    Route::prefix('academic')->name('academic.')->group(function () {

        // Sessions & Terms
        Route::resource('sessions', Academic\SessionController::class)->only(['index', 'store']);
        Route::post('sessions/{session}/set-current', [Academic\SessionController::class, 'setAsCurrent'])->name('sessions.set-current');

        // Classes & Arms
        Route::resource('classes', Academic\ClassController::class);

        // Subjects
        Route::resource('subjects', Academic\SubjectController::class)->only(['index', 'store', 'update', 'destroy']);

        // Students
        Route::resource('students', Academic\StudentController::class);
        Route::post('students/promote', [Academic\StudentController::class, 'promote'])->name('students.promote');

        // Attendance
        Route::get('attendance', [Academic\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance', [Academic\AttendanceController::class, 'store'])->name('attendance.store');

        // Timetable
        Route::get('timetable', [Academic\TimetableController::class, 'index'])->name('timetable.index');
        Route::post('timetable', [Academic\TimetableController::class, 'store'])->name('timetable.store');
        Route::delete('timetable/{timetable}', [Academic\TimetableController::class, 'destroy'])->name('timetable.destroy');
    });

    // ── Admission Module ───────────────────────────────────
    Route::prefix('admission')->name('admission.')->group(function () {
        Route::get('/', [AdmissionController::class, 'index'])->name('index');
        Route::get('/create', [AdmissionController::class, 'create'])->name('create');
        Route::post('/', [AdmissionController::class, 'store'])->name('store');
        Route::get('/{admission}', [AdmissionController::class, 'show'])->name('show');
        Route::post('/{admission}/review', [AdmissionController::class, 'review'])->name('review');
        Route::post('/{admission}/enroll', [AdmissionController::class, 'enroll'])->name('enroll');
    });

    // ── Financial Module ───────────────────────────────────
    Route::prefix('financial')->name('financial.')->group(function () {
        // Fee Structures
        Route::resource('fees', Financial\FeeController::class)->only(['index', 'create', 'store', 'update', 'destroy']);

        // Invoices
        Route::get('invoices', [Financial\InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices/generate', [Financial\InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::get('invoices/{invoice}', [Financial\InvoiceController::class, 'show'])->name('invoices.show');

        // Payments
        Route::get('payments', [Financial\PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments/manual', [Financial\PaymentController::class, 'recordManual'])->name('payments.manual');
        Route::post('payments/paystack', [Financial\PaymentController::class, 'initiatePaystack'])->name('payments.paystack');
        Route::get('payments/paystack/callback', [Financial\PaymentController::class, 'paystackCallback'])->name('payments.paystack.callback');
    });

    // ── Examination / Results ──────────────────────────────
    Route::prefix('examination')->name('examination.')->group(function () {
        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/enter-scores', [ResultController::class, 'enterScores'])->name('results.enter-scores');
        Route::post('results/store-scores', [ResultController::class, 'storeScores'])->name('results.store-scores');
        Route::post('results/compute', [ResultController::class, 'computeResults'])->name('results.compute');
        Route::post('results/approve', [ResultController::class, 'approveResults'])->name('results.approve');
        Route::get('report-card/{student}/{term}', [ResultController::class, 'reportCard'])->name('results.report-card');
        Route::get('report-card/{student}/{term}/download', [ResultController::class, 'downloadReportCard'])->name('results.report-card.download');
    });

    // ── Staff Module ───────────────────────────────────────
    Route::resource('staff', StaffController::class);

    // ── Communication ──────────────────────────────────────
    Route::resource('announcements', AnnouncementController::class)->only(['index', 'store', 'destroy']);

    // ── Library ────────────────────────────────────────────
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::post('/books', [LibraryController::class, 'store'])->name('store');
        Route::post('/borrow', [LibraryController::class, 'borrow'])->name('borrow');
        Route::post('/return/{borrowing}', [LibraryController::class, 'returnBook'])->name('return');
    });

    // ── Transport ──────────────────────────────────────────
    Route::resource('transport', TransportController::class)->only(['index', 'store', 'update']);

    // ── Hostel ─────────────────────────────────────────────
    Route::get('hostel', [HostelController::class, 'index'])->name('hostel.index');
    Route::post('hostel', [HostelController::class, 'store'])->name('hostel.store');
    Route::post('hostel/allocate', [HostelController::class, 'allocateRoom'])->name('hostel.allocate');

    // ── Health ─────────────────────────────────────────────
    Route::get('health', [MedicalController::class, 'index'])->name('health.index');
    Route::post('health', [MedicalController::class, 'store'])->name('health.store');

    // ── Inventory ──────────────────────────────────────────
    Route::resource('assets', AssetController::class)->only(['index', 'store', 'update']);

    // ── Reporting ──────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('financial', [ReportController::class, 'financialDashboard'])->name('financial');
        Route::get('academic', [ReportController::class, 'academicDashboard'])->name('academic');
        Route::get('attendance', [ReportController::class, 'attendanceReport'])->name('attendance');
    });

    // ── Settings ───────────────────────────────────────────
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('settings/system', [SettingsController::class, 'updateSettings'])->name('settings.system');
    Route::post('settings/system/test-smtp', [SettingsController::class, 'sendSmtpTest'])->name('settings.smtp-test');
    Route::post('settings/public-page/reset-theme', [SettingsController::class, 'resetThemeDefaults'])->name('settings.public-page.reset-theme');
    Route::put('settings/public-page', [SettingsController::class, 'updatePublicPage'])->name('settings.public-page');
    Route::prefix('system')->name('system.')->group(function () {
        Route::resource('hero-slides', HeroSlideController::class)->except('show');
        Route::patch('hero-slides/{heroSlide}/toggle', [HeroSlideController::class, 'toggle'])->name('hero-slides.toggle');
        Route::get('testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
        Route::patch('testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
        Route::patch('testimonials/{testimonial}/reject', [TestimonialController::class, 'reject'])->name('testimonials.reject');
    });

    // ── Multi-School / SaaS (Super Admin only) ─────────────
    Route::prefix('admin')->name('multi-school.')->group(function () {
        Route::get('/dashboard', [TenantController::class, 'index'])->name('index');
        Route::post('/onboard', [TenantController::class, 'onboard'])->name('onboard');
    });
});

