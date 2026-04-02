<?php

namespace App\Http\Controllers\MultiSchool;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $this->ensureSuperAdmin();

        $schools = School::withCount(['students', 'staff'])->latest()->paginate(20);
        $summary = [
            'totalSchools' => School::count(),
            'activeSchools' => School::where('is_active', true)->count(),
            'schoolsOnPage' => $schools->count(),
        ];

        return view('multi-school.index', compact('schools', 'summary'));
    }

    public function onboard(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:schools,email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'school_type' => 'required|in:primary,secondary,combined',
            'plan' => 'required|in:basic,standard,premium,enterprise',
            'admin_name' => 'required|string',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        $school = School::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'code' => strtoupper(Str::random(6)),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'school_type' => $validated['school_type'],
            'subscription_plan' => $validated['plan'],
            'subscription_expires_at' => now()->addYear(),
        ]);

        $nameParts = explode(' ', $validated['admin_name'], 2);
        User::create([
            'school_id' => $school->id,
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? $nameParts[0],
            'email' => $validated['admin_email'],
            'password' => Hash::make('changeme123'),
            'role' => 'school_admin',
        ]);

        Subscription::create([
            'school_id' => $school->id,
            'plan_name' => ucfirst($validated['plan']),
            'plan_code' => $validated['plan'],
            'amount' => $this->planPrice($validated['plan']),
            'billing_cycle' => 'yearly',
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);

        return redirect()->route('multi-school.index')->with('success', "School {$school->name} onboarded.");
    }

    private function planPrice(string $plan): float
    {
        return match($plan) {
            'basic' => 50000,
            'standard' => 150000,
            'premium' => 350000,
            'enterprise' => 750000,
            default => 50000,
        };
    }

    private function ensureSuperAdmin(): void
    {
        $user = auth()->user();

        if (!$user || ($user->role?->value ?? null) !== UserRole::SUPER_ADMIN->value) {
            abort(403, 'Unauthorized.');
        }
    }
}
