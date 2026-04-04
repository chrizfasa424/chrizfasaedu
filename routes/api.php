<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - ChrizFasa Academy
|--------------------------------------------------------------------------
| Mobile app and third-party integration endpoints.
| All routes are prefixed with /api and use Sanctum authentication.
*/

Route::prefix('v1')->group(function () {

    // Public
    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'role']),
            'token' => $token,
        ]);
    });

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', fn() => auth()->user()->load('school'));

        // Student endpoints
        Route::get('/students', function () {
            return \App\Models\Student::with('schoolClass')->active()->paginate(25);
        });

        Route::get('/students/{student}', function (\App\Models\Student $student) {
            return $student->load(['schoolClass', 'arm', 'parents']);
        });

        // Results
        Route::get('/students/{student}/results', function (\App\Models\Student $student) {
            return $student->results()->with('subject')->where('is_approved', true)->get();
        });

        // Attendance
        Route::get('/students/{student}/attendance', function (\App\Models\Student $student) {
            return $student->attendances()->latest()->paginate(30);
        });

        // Invoices & Payments
        Route::get('/students/{student}/invoices', function (\App\Models\Student $student) {
            return $student->invoices()->with('payments')->latest()->get();
        });

        // Timetable
        Route::get('/timetable/{classId}', function ($classId) {
            return \App\Models\Timetable::with(['subject', 'teacher.user'])
                ->where('class_id', $classId)
                ->where('is_active', true)
                ->get()
                ->groupBy('day_of_week');
        });

        // Announcements
        Route::get('/announcements', function () {
            return \App\Models\Announcement::latest()->take(20)->get();
        });

        Route::post('/logout', function () {
            auth()->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out']);
        });
    });

    // Webhooks (no auth)
    Route::post('/webhooks/paystack', function (\Illuminate\Http\Request $request) {
        // Verify Paystack webhook signature
        $signature = $request->header('x-paystack-signature');
        $computed = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret'));

        if ($signature !== $computed) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'charge.success') {
            $payment = \App\Models\Payment::where('payment_reference', $data['reference'])->first();
            if ($payment && $payment->status !== 'confirmed') {
                $payment->update([
                    'status' => 'confirmed',
                    'transaction_id' => $data['id'],
                    'gateway_response' => $data,
                    'paid_at' => now(),
                ]);
                $payment->invoice->updateBalance();
            }
        }

        return response()->json(['status' => 'ok']);
    });
});

