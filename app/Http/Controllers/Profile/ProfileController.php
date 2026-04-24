<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    private function user(): \App\Models\User
    {
        // Works for both web and portal guards
        return Auth::guard('portal')->check()
            ? Auth::guard('portal')->user()
            : Auth::guard('web')->user();
    }

    // ── Show profile page ──────────────────────────────────
    public function show()
    {
        $user = $this->user();
        $profile = $this->resolveProfile($user);
        if ($user->isStudent() && $profile) {
            $profile->loadMissing(['schoolClass', 'arm']);
        }
        return view('profile.show', compact('user', 'profile'));
    }

    // ── Update basic info (User fields) ───────────────────
    public function updateInfo(Request $request)
    {
        $user = $this->user();

        $validated = $request->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'other_names' => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:20',
            'email'       => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Update profile-specific fields (Student/Staff/Parent) ──
    public function updateProfileDetails(Request $request)
    {
        $user    = $this->user();
        $profile = $this->resolveProfile($user);

        if (!$profile) {
            return back()->with('error', 'No extended profile found.');
        }

        $validated = $request->validate([
            'address'    => 'nullable|string|max:500',
            'city'       => 'nullable|string|max:100',
            'state'      => 'nullable|string|max:100',
            'nationality'=> 'nullable|string|max:100',
            'religion'   => 'nullable|string|max:50',
            'blood_group'=> 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'genotype'   => 'nullable|string|in:AA,AS,AC,SS,SC',
            // Staff-only
            'designation'=> 'nullable|string|max:100',
            'qualification' => 'nullable|string|max:200',
            // Student emergency contact
            'emergency_contact_name'  => 'nullable|string|max:200',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $profile->update(array_filter($validated, fn($v) => $v !== null));

        return back()->with('success', 'Details updated successfully.');
    }

    // ── Change password ────────────────────────────────────
    public function changePassword(Request $request)
    {
        $user = $this->user();

        $request->validate([
            'current_password' => ['required', function ($attr, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return back()
            ->with('success', 'Password changed successfully.')
            ->with('profile_tab', 'password');
    }

    // ── Upload profile photo ───────────────────────────────
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user    = $this->user();
        $profile = $this->resolveProfile($user);

        // Delete old photo
        $old = $profile?->photo ?? $user->avatar;
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        $path = $request->file('photo')->store('avatars', 'public');

        // Store on profile if it has one, otherwise on the user avatar field
        if ($profile && in_array('photo', $profile->getFillable())) {
            $profile->update(['photo' => $path]);
        } else {
            $user->update(['avatar' => $path]);
        }

        return back()->with('success', 'Profile photo updated.');
    }

    // ── Delete profile photo ───────────────────────────────
    public function deletePhoto()
    {
        $user    = $this->user();
        $profile = $this->resolveProfile($user);

        $photo = $profile?->photo ?? $user->avatar;
        if ($photo && Storage::disk('public')->exists($photo)) {
            Storage::disk('public')->delete($photo);
        }

        if ($profile && in_array('photo', $profile->getFillable())) {
            $profile->update(['photo' => null]);
        } else {
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Profile photo removed.');
    }

    // ── Resolve extended profile ───────────────────────────
    private function resolveProfile(\App\Models\User $user): ?\Illuminate\Database\Eloquent\Model
    {
        return match(true) {
            $user->isStudent()  => $user->student,
            $user->isTeacher()  => $user->staffProfile,
            $user->isParent()   => $user->parentProfile,
            // Admins use User model itself — no separate profile table
            default             => $user->staffProfile ?? null,
        };
    }
}
