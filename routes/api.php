<?php

use App\Http\Controllers\Financial\PaymentWebhookController;
use App\Models\Announcement;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - ChrizFasa Academy
|--------------------------------------------------------------------------
| Mobile app and third-party integration endpoints.
| All routes are prefixed with /api and use Sanctum authentication.
*/

Route::prefix('v1')->group(function () {
    $roleOf = static fn (User $user): string => (string) ($user->role?->value ?? $user->role ?? '');

    $canUseSchoolWideEndpoints = static fn (string $role): bool => in_array($role, [
        'super_admin',
        'school_admin',
        'principal',
        'vice_principal',
        'teacher',
        'staff',
        'accountant',
        'librarian',
        'driver',
        'nurse',
    ], true);

    $canAccessStudent = function (User $user, Student $student) use ($roleOf, $canUseSchoolWideEndpoints): bool {
        $role = $roleOf($user);

        if ($role === 'super_admin') {
            return true;
        }

        if ((int) $user->school_id > 0 && (int) $student->school_id !== (int) $user->school_id) {
            return false;
        }

        if ($role === 'student') {
            return (int) ($user->student?->id ?? 0) === (int) $student->id;
        }

        if ($role === 'parent') {
            $childrenQuery = $user->parentProfile?->children();

            return $childrenQuery
                ? $childrenQuery->where('students.id', (int) $student->id)->exists()
                : false;
        }

        return $canUseSchoolWideEndpoints($role);
    };

    // Public
    Route::post('/login', function (Request $request) use ($roleOf) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $normalizedEmail = mb_strtolower(trim((string) $credentials['email']));
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->first();

        if (!$user || !(bool) $user->is_active) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!Auth::attempt([
            'email' => (string) $user->email,
            'password' => (string) $credentials['password'],
            'is_active' => true,
        ])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /** @var User $authenticated */
        $authenticated = Auth::user();
        $role = $roleOf($authenticated);

        $abilities = ['profile:read'];
        if (in_array($role, ['student', 'parent'], true)) {
            $abilities[] = 'portal:self';
        } else {
            $abilities[] = 'school:read';
        }

        $authenticated->tokens()->where('name', 'mobile-app')->delete();
        $token = $authenticated->createToken('mobile-app', $abilities)->plainTextToken;

        return response()->json([
            'user' => [
                'id' => (int) $authenticated->id,
                'first_name' => (string) $authenticated->first_name,
                'last_name' => (string) $authenticated->last_name,
                'email' => (string) $authenticated->email,
                'role' => $role,
                'school_id' => (int) ($authenticated->school_id ?? 0),
            ],
            'token' => $token,
        ]);
    })->middleware('throttle:8,1');

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () use ($roleOf, $canUseSchoolWideEndpoints, $canAccessStudent) {
        Route::get('/user', function () use ($roleOf) {
            /** @var User $user */
            $user = auth()->user();
            $user->loadMissing('school');

            return response()->json([
                'id' => (int) $user->id,
                'first_name' => (string) $user->first_name,
                'last_name' => (string) $user->last_name,
                'email' => (string) $user->email,
                'role' => $roleOf($user),
                'school' => $user->school?->only(['id', 'name', 'code', 'domain']),
            ]);
        });

        // Student endpoints
        Route::get('/students', function () use ($roleOf, $canUseSchoolWideEndpoints) {
            /** @var User $user */
            $user = auth()->user();
            $role = $roleOf($user);

            $query = Student::query()
                ->with('schoolClass')
                ->active();

            if ($role === 'student') {
                $studentId = (int) ($user->student?->id ?? 0);
                if ($studentId <= 0) {
                    return response()->json(['message' => 'Student profile is not available.'], 403);
                }

                $query->whereKey($studentId);
            } elseif ($role === 'parent') {
                $childIds = $user->parentProfile?->children()->pluck('students.id')->all() ?? [];
                if (empty($childIds)) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->whereIn('id', $childIds);
                }
            } elseif (!$canUseSchoolWideEndpoints($role)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return $query->paginate(25);
        });

        Route::get('/students/{student}', function (Student $student) use ($canAccessStudent) {
            /** @var User $user */
            $user = auth()->user();

            if (!$canAccessStudent($user, $student)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return $student->load(['schoolClass', 'arm', 'parents']);
        });

        // Results
        Route::get('/students/{student}/results', function (Student $student) use ($canAccessStudent) {
            /** @var User $user */
            $user = auth()->user();

            if (!$canAccessStudent($user, $student)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return $student->results()
                ->with('subject')
                ->where('is_approved', true)
                ->get();
        });

        // Attendance
        Route::get('/students/{student}/attendance', function (Student $student) use ($canAccessStudent) {
            /** @var User $user */
            $user = auth()->user();

            if (!$canAccessStudent($user, $student)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return $student->attendances()->latest()->paginate(30);
        });

        // Invoices & Payments
        Route::get('/students/{student}/invoices', function (Student $student) use ($canAccessStudent) {
            /** @var User $user */
            $user = auth()->user();

            if (!$canAccessStudent($user, $student)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return $student->invoices()->with('payments')->latest()->get();
        });

        // Timetable
        Route::get('/timetable/{classId}', function ($classId) use ($roleOf, $canUseSchoolWideEndpoints) {
            /** @var User $user */
            $user = auth()->user();
            $role = $roleOf($user);
            $classId = (int) $classId;

            if ($classId <= 0) {
                return response()->json(['message' => 'Invalid class id.'], 422);
            }

            if ($role === 'student') {
                $studentClassId = (int) ($user->student?->class_id ?? 0);
                if ($studentClassId <= 0 || $studentClassId !== $classId) {
                    return response()->json(['message' => 'Unauthorized.'], 403);
                }
            } elseif ($role === 'parent') {
                $classIds = $user->parentProfile?->children()->pluck('students.class_id')->filter()->unique()->values()->all() ?? [];
                if (!in_array($classId, $classIds, true)) {
                    return response()->json(['message' => 'Unauthorized.'], 403);
                }
            } elseif (!$canUseSchoolWideEndpoints($role)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            $query = Timetable::query()
                ->with(['subject', 'teacher.user'])
                ->where('class_id', $classId)
                ->where('is_active', true);

            if ($role !== 'super_admin' && (int) $user->school_id > 0) {
                $query->where('school_id', (int) $user->school_id);
            }

            return $query->get()->groupBy('day_of_week');
        });

        // Announcements
        Route::get('/announcements', function () use ($roleOf, $canUseSchoolWideEndpoints) {
            /** @var User $user */
            $user = auth()->user();
            $role = $roleOf($user);

            $query = Announcement::query()
                ->where(function ($scope) {
                    $scope->whereNull('published_at')->orWhere('published_at', '<=', now());
                })
                ->where(function ($scope) {
                    $scope->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->latest();

            if ($role === 'student') {
                $classId = (int) ($user->student?->class_id ?? 0);
                $query->where(function ($scope) use ($classId) {
                    $scope->whereIn('audience', ['all', 'students']);
                    if ($classId > 0) {
                        $scope->orWhere(function ($nested) use ($classId) {
                            $nested->where('audience', 'specific_class')
                                ->where('target_class_id', $classId);
                        });
                    }
                });
            } elseif ($role === 'parent') {
                $classIds = $user->parentProfile?->children()->pluck('students.class_id')->filter()->unique()->values()->all() ?? [];
                $query->where(function ($scope) use ($classIds) {
                    $scope->whereIn('audience', ['all', 'parents']);
                    if (!empty($classIds)) {
                        $scope->orWhere(function ($nested) use ($classIds) {
                            $nested->where('audience', 'specific_class')
                                ->whereIn('target_class_id', $classIds);
                        });
                    }
                });
            } elseif (!$canUseSchoolWideEndpoints($role)) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            return $query->take(20)->get();
        });

        Route::post('/logout', function () {
            $token = auth()->user()?->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            return response()->json(['message' => 'Logged out']);
        });
    });

    // Webhooks (no auth)
    Route::post('/webhooks/paystack', [PaymentWebhookController::class, 'paystack'])
        ->middleware('throttle:120,1');
});
