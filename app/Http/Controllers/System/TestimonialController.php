<?php

namespace App\Http\Controllers\System;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;

class TestimonialController extends Controller
{
    public function index()
    {
        $this->ensureAdminAccess();

        $baseQuery = $this->scopedTestimonialsQuery();

        $pendingTestimonials = (clone $baseQuery)
            ->where('status', 'pending')
            ->latest()
            ->paginate(15, ['*'], 'pending_page');

        $approvedTestimonials = (clone $baseQuery)
            ->where('status', 'approved')
            ->latest('reviewed_at')
            ->latest('id')
            ->paginate(15, ['*'], 'approved_page');

        $rejectedTestimonials = (clone $baseQuery)
            ->where('status', 'rejected')
            ->latest('reviewed_at')
            ->latest('id')
            ->paginate(15, ['*'], 'rejected_page');

        return view('system.testimonials.index', compact(
            'pendingTestimonials',
            'approvedTestimonials',
            'rejectedTestimonials'
        ));
    }

    public function approve(int $testimonial)
    {
        $this->ensureAdminAccess();

        $record = $this->scopedTestimonialsQuery()->findOrFail($testimonial);

        $record->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Testimonial approved successfully.');
    }

    public function reject(int $testimonial)
    {
        $this->ensureAdminAccess();

        $record = $this->scopedTestimonialsQuery()->findOrFail($testimonial);

        $record->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Testimonial rejected.');
    }

    private function ensureAdminAccess(): void
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role?->value, [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
        ], true)) {
            abort(403, 'Unauthorized.');
        }
    }

    private function scopedTestimonialsQuery(): Builder
    {
        $user = auth()->user();
        $query = Testimonial::query();

        if (($user?->role?->value ?? null) !== UserRole::SUPER_ADMIN->value) {
            $query->where('school_id', $user?->school_id);
        }

        return $query;
    }
}
