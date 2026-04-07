<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Academic;
use App\Http\Controllers\Admission\AdmissionController;
use App\Http\Controllers\Financial;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Examination\ResultController;
use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Library\LibraryController;
use App\Http\Controllers\Portal;
use App\Http\Controllers\Portal\PortalMessageController;
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

Route::get('/admin-access', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin-access', [LoginController::class, 'login']);
Route::get('/portal', [LoginController::class, 'showPortalLoginForm'])->name('portal.login');
Route::post('/portal', [LoginController::class, 'portalLogin'])->name('portal.login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/portal/logout', [LoginController::class, 'portalLogout'])->name('portal.logout');

// Admin password reset
Route::get('/admin-access/forgot-password', [PasswordResetController::class, 'showAdminForgotForm'])->name('admin.password.request');
Route::post('/admin-access/forgot-password', [PasswordResetController::class, 'sendAdminResetLink'])->name('admin.password.email');
Route::get('/admin-access/reset-password/{token}', [PasswordResetController::class, 'showAdminResetForm'])->name('admin.password.reset');
Route::post('/admin-access/reset-password', [PasswordResetController::class, 'resetAdminPassword'])->name('admin.password.update');

// Portal (student/parent) password reset
Route::get('/portal/forgot-password', [PasswordResetController::class, 'showPortalForgotForm'])->name('portal.password.request');
Route::post('/portal/forgot-password', [PasswordResetController::class, 'sendPortalResetLink'])->name('portal.password.email');
Route::get('/portal/reset-password/{token}', [PasswordResetController::class, 'showPortalResetForm'])->name('portal.password.reset');
Route::post('/portal/reset-password', [PasswordResetController::class, 'resetPortalPassword'])->name('portal.password.update');

// Online Admission (public)
Route::get('/apply', [AdmissionController::class, 'applyOnline'])->name('admission.apply');
Route::get('/apply/success', [AdmissionController::class, 'success'])->name('admission.success');
Route::post('/apply', [AdmissionController::class, 'store'])->name('admission.apply.store')->middleware('throttle:5,10');

// ── Authenticated Routes ───────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Profile (admin / staff / teacher — web guard) ──────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                   [ProfileController::class, 'show'])->name('show');
        Route::post('/info',              [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::post('/details',           [ProfileController::class, 'updateProfileDetails'])->name('update.details');
        Route::post('/password',          [ProfileController::class, 'changePassword'])->name('change.password');
        Route::post('/photo',             [ProfileController::class, 'updatePhoto'])->name('update.photo');
        Route::delete('/photo',           [ProfileController::class, 'deletePhoto'])->name('delete.photo');
    });

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/teacher/dashboard', [DashboardController::class, 'teacherDashboard'])->name('teacher.dashboard');

    // ── Academic Module ────────────────────────────────────
    Route::prefix('academic')->name('academic.')->group(function () {

        // Sessions & Terms
        Route::resource('sessions', Academic\SessionController::class)->only(['index', 'store']);
        Route::post('sessions/{session}/set-current', [Academic\SessionController::class, 'setAsCurrent'])->name('sessions.set-current');

        // Terms (nested under sessions view)
        Route::post('sessions/{session}/terms', [Academic\TermController::class, 'store'])->name('terms.store');
        Route::put('terms/{term}', [Academic\TermController::class, 'update'])->name('terms.update');
        Route::delete('terms/{term}', [Academic\TermController::class, 'destroy'])->name('terms.destroy');
        Route::post('terms/{term}/set-current', [Academic\TermController::class, 'setCurrent'])->name('terms.set-current');

        // Classes & Arms
        Route::resource('classes', Academic\ClassController::class);

        // Subjects
        Route::resource('subjects', Academic\SubjectController::class)->only(['index', 'store', 'update', 'destroy']);

        // Students
        Route::resource('students', Academic\StudentController::class);
        Route::post('students/promote', [Academic\StudentController::class, 'promote'])->name('students.promote');
        Route::post('students/{student}/toggle-active', [Academic\StudentController::class, 'toggleActive'])->name('students.toggle-active');
        Route::post('students/{student}/reset-password', [Academic\StudentController::class, 'resetPassword'])->name('students.reset-password');
        Route::post('students/{student}/change-password', [Academic\StudentController::class, 'changePassword'])->name('students.change-password');

        // Attendance
        Route::get('attendance', [Academic\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance', [Academic\AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('attendance/history', [Academic\AttendanceController::class, 'history'])->name('attendance.history');

        // Timetable
        Route::get('timetable', [Academic\TimetableController::class, 'index'])->name('timetable.index');
        Route::post('timetable', [Academic\TimetableController::class, 'store'])->name('timetable.store');
        Route::delete('timetable/{timetable}', [Academic\TimetableController::class, 'destroy'])->name('timetable.destroy');
    });

    // ── Admission Module ───────────────────────────────────
    Route::prefix('admission')->name('admission.')->group(function () {
        Route::get('/', [AdmissionController::class, 'index'])->name('index');
        Route::get('/{admission}', [AdmissionController::class, 'show'])->name('show');
        Route::patch('/{admission}/review', [AdmissionController::class, 'review'])->name('review');
        Route::post('/{admission}/enroll', [AdmissionController::class, 'enroll'])->name('enroll');
        Route::delete('/{admission}', [AdmissionController::class, 'destroy'])->name('destroy');
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
        Route::get('results/import', [ResultController::class, 'importForm'])->name('results.import');
        Route::post('results/import', [ResultController::class, 'import'])->name('results.import.store');
        Route::get('results/template', [ResultController::class, 'downloadTemplate'])->name('results.template');
        Route::get('results/export', [ResultController::class, 'export'])->name('results.export');
        Route::put('results/{result}', [ResultController::class, 'update'])->name('results.update');
        Route::delete('results/{result}', [ResultController::class, 'destroy'])->name('results.destroy');
        Route::get('report-card/{student}/{term}', [ResultController::class, 'reportCard'])->name('results.report-card');
        Route::get('report-card/{student}/{term}/download', [ResultController::class, 'downloadReportCard'])->name('results.report-card.download');
        Route::get('results/{student}/{term}/download-excel', [ResultController::class, 'exportStudentResults'])->name('results.student-export');
    });

    // ── Staff Module ───────────────────────────────────────
    Route::resource('staff', StaffController::class);

    // ── Communication ──────────────────────────────────────
    Route::resource('announcements', AnnouncementController::class)->only(['index', 'store', 'destroy']);

    // ── Messaging (admin) ──────────────────────────────────
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/unread-count',  [MessageController::class, 'unreadCount'])->name('unread-count');
        Route::get('/',              [MessageController::class, 'index'])->name('index');
        Route::get('/create',        [MessageController::class, 'create'])->name('create');
        Route::post('/',             [MessageController::class, 'store'])->name('store');
        Route::get('/{message}',     [MessageController::class, 'show'])->name('show');
        Route::delete('/{message}',  [MessageController::class, 'destroy'])->name('destroy');
    });

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
    Route::get('settings/{page}', [SettingsController::class, 'showPage'])->name('settings.page');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('settings/system', [SettingsController::class, 'updateSettings'])->name('settings.system');
    Route::post('settings/rich-text/upload', [SettingsController::class, 'uploadRichTextImage'])
        ->middleware('throttle:20,1')
        ->name('settings.rich-text.upload');
    Route::post('settings/system/test-smtp', [SettingsController::class, 'sendSmtpTest'])->name('settings.smtp-test');
    Route::post('settings/public-page/reset-theme', [SettingsController::class, 'resetThemeDefaults'])->name('settings.public-page.reset-theme');
    Route::put('settings/public-page', [SettingsController::class, 'updatePublicPage'])->name('settings.public-page');
    Route::post('settings/submenu-image/upload', [SettingsController::class, 'uploadSubmenuImage'])->name('settings.submenu-image.upload');
    Route::post('settings/submenu-image/remove', [SettingsController::class, 'removeSubmenuImage'])->name('settings.submenu-image.remove');
    Route::post('settings/submenu-content/save', [SettingsController::class, 'saveSubmenuContent'])->name('settings.submenu-content.save');
    Route::post('settings/submenu-content-image/upload', [SettingsController::class, 'uploadSubmenuContentImage'])->name('settings.submenu-content-image.upload');
    Route::post('settings/submenu-content-image/remove', [SettingsController::class, 'removeSubmenuContentImage'])->name('settings.submenu-content-image.remove');
    Route::post('settings/faqs/save', [SettingsController::class, 'saveFaqs'])->name('settings.faqs.save');
    Route::prefix('system')->name('system.')->group(function () {
        Route::resource('hero-slides', HeroSlideController::class)->except('show');
        Route::patch('hero-slides/{heroSlide}/toggle', [HeroSlideController::class, 'toggle'])->name('hero-slides.toggle');
        Route::patch('hero-slides/reorder', [HeroSlideController::class, 'reorder'])->name('hero-slides.reorder');
        Route::get('testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
        Route::get('testimonials/bulk', fn() => redirect()->route('system.testimonials.index'));
        Route::post('testimonials/bulk', [TestimonialController::class, 'bulkAction'])->name('testimonials.bulk');
        Route::post('testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
        Route::post('testimonials/{testimonial}/reject', [TestimonialController::class, 'reject'])->name('testimonials.reject');
        Route::post('testimonials/{testimonial}/delete', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
    });

    // ── Multi-School / SaaS (Super Admin only) ─────────────
    Route::prefix('admin')->name('multi-school.')->group(function () {
        Route::get('/dashboard', [TenantController::class, 'index'])->name('index');
        Route::get('/domains', [TenantController::class, 'domains'])->name('domains');
        Route::post('/onboard', [TenantController::class, 'onboard'])->name('onboard');
        Route::post('/domains/clear-cache', [TenantController::class, 'clearDomainCache'])->name('domains.clear-cache');
        Route::put('/domains/{school}', [TenantController::class, 'updateDomain'])->name('domains.update');
    });
});

// ── Student / Parent Portal Routes (portal guard) ──────────
Route::middleware(['portal.guard', 'auth:portal'])->group(function () {
    Route::get('/student/dashboard', [Portal\StudentPortalController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/student/testimonials', [Portal\StudentPortalController::class, 'submitTestimonial'])
        ->middleware('throttle:3,10')
        ->name('student.testimonials.submit');
    Route::get('/my/results/{term}', [Portal\StudentPortalController::class, 'reportCard'])->name('portal.results.report-card');
    Route::get('/my/results/{term}/download', [Portal\StudentPortalController::class, 'downloadReportCard'])->name('portal.results.report-card.download');
    Route::get('/my/results/{term}/download-excel', [Portal\StudentPortalController::class, 'downloadResultExcel'])->name('portal.results.download-excel');
    Route::get('/my/attendance', [Academic\AttendanceController::class, 'portalView'])->name('portal.attendance');
    Route::get('/parent/dashboard', [Portal\ParentPortalController::class, 'dashboard'])->name('parent.dashboard');

    // ── Messaging (portal) ────────────────────────────────
    Route::prefix('my/messages')->name('portal.messages.')->group(function () {
        Route::get('/unread-count',       [PortalMessageController::class, 'unreadCount'])->name('unread-count');
        Route::get('/',                   [PortalMessageController::class, 'index'])->name('index');
        Route::get('/{message}',          [PortalMessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply',   [PortalMessageController::class, 'reply'])->name('reply');
    });

    // ── Profile (student / parent — portal guard) ──────────
    Route::prefix('my/profile')->name('portal.profile.')->group(function () {
        Route::get('/',          [ProfileController::class, 'show'])->name('show');
        Route::post('/info',     [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::post('/details',  [ProfileController::class, 'updateProfileDetails'])->name('update.details');
        Route::post('/password', [ProfileController::class, 'changePassword'])->name('change.password');
        Route::post('/photo',    [ProfileController::class, 'updatePhoto'])->name('update.photo');
        Route::delete('/photo',  [ProfileController::class, 'deletePhoto'])->name('delete.photo');
    });
});

