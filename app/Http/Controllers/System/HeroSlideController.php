<?php

namespace App\Http\Controllers\System;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HeroSlideController extends Controller
{
    public function index(): View
    {
        $this->ensureAdminAccess();

        $slides = $this->scopedSlidesQuery()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('system.hero-slides.index', [
            'slides' => $slides,
            'maxSlides' => 4,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $this->ensureAdminAccess();

        if ($this->scopedSlidesQuery()->count() >= 4) {
            return redirect()
                ->route('system.hero-slides.index')
                ->with('error', 'You can only create up to 4 hero slides.');
        }

        return view('system.hero-slides.create', [
            'slide' => new HeroSlide([
                'order' => max(1, $this->scopedSlidesQuery()->count() + 1),
                'is_active' => true,
            ]),
            'maxSlides' => 4,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        if ($this->scopedSlidesQuery()->count() >= 4) {
            return redirect()
                ->route('system.hero-slides.index')
                ->with('error', 'You can only create up to 4 hero slides.');
        }

        $validated = $this->validateSlide($request, null);
        $validated['is_active'] = $request->boolean('is_active');
        $this->guardActiveSlideLimit((bool) ($validated['is_active'] ?? false));

        $slide = new HeroSlide($validated);
        $slide->school_id = auth()->user()->school_id;
        $slide->image_path = $request->file('image')->store('hero-slides', 'public');
        $slide->save();

        $this->syncSlideOrder($slide, (int) $validated['order']);

        return redirect()
            ->route('system.hero-slides.index')
            ->with('success', 'Hero slide created successfully.');
    }

    public function edit(int $heroSlide): View
    {
        $this->ensureAdminAccess();

        $slide = $this->scopedSlidesQuery()->findOrFail($heroSlide);

        return view('system.hero-slides.edit', [
            'slide' => $slide,
            'maxSlides' => 4,
        ]);
    }

    public function update(Request $request, int $heroSlide): RedirectResponse
    {
        $this->ensureAdminAccess();

        $slide = $this->scopedSlidesQuery()->findOrFail($heroSlide);
        $validated = $this->validateSlide($request, $slide);
        $validated['is_active'] = $request->boolean('is_active');
        $this->guardActiveSlideLimit((bool) ($validated['is_active'] ?? false), $slide->id);

        if ($request->hasFile('image')) {
            $this->deleteImageIfPresent($slide->image_path);
            $validated['image_path'] = $request->file('image')->store('hero-slides', 'public');
        }

        $slide->fill($validated);
        $slide->save();

        $this->syncSlideOrder($slide, (int) $validated['order']);

        return redirect()
            ->route('system.hero-slides.index')
            ->with('success', 'Hero slide updated successfully.');
    }

    public function destroy(int $heroSlide): RedirectResponse
    {
        $this->ensureAdminAccess();

        $slide = $this->scopedSlidesQuery()->findOrFail($heroSlide);
        $this->deleteImageIfPresent($slide->image_path);
        $slide->delete();

        $this->normalizeSlideOrder();

        return redirect()
            ->route('system.hero-slides.index')
            ->with('success', 'Hero slide deleted successfully.');
    }

    public function toggle(Request $request, int $heroSlide): RedirectResponse
    {
        $this->ensureAdminAccess();

        $slide = $this->scopedSlidesQuery()->findOrFail($heroSlide);
        $isActive = $request->boolean('is_active');

        $this->guardActiveSlideLimit($isActive, $slide->id);

        $slide->update([
            'is_active' => $isActive,
        ]);

        return redirect()
            ->route('system.hero-slides.index')
            ->with('success', $isActive ? 'Hero slide activated.' : 'Hero slide deactivated.');
    }

    public function reorder(Request $request)
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1', 'max:4'],
            'order.*' => ['required', 'integer'],
        ]);

        $slides = $this->scopedSlidesQuery()
            ->whereIn('id', $validated['order'])
            ->get()
            ->keyBy('id');

        if ($slides->count() !== count($validated['order'])) {
            abort(422, 'Unable to reorder hero slides.');
        }

        collect($validated['order'])
            ->values()
            ->each(function (int $slideId, int $index) use ($slides): void {
                $slide = $slides->get($slideId);

                if (!$slide) {
                    return;
                }

                $slide->timestamps = false;
                $slide->order = $index + 1;
                $slide->saveQuietly();
            });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Hero slide order updated successfully.',
            ]);
        }

        return redirect()
            ->route('system.hero-slides.index')
            ->with('success', 'Hero slide order updated successfully.');
    }

    private function validateSlide(Request $request, ?HeroSlide $slide): array
    {
        $imageRule = $slide ? 'nullable' : 'required';

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:1500'],
            'badge_text' => ['required', 'string', 'max:120'],
            'button_1_text' => ['required', 'string', 'max:80'],
            'button_1_link' => ['required', 'string', 'max:255'],
            'button_2_text' => ['required', 'string', 'max:80'],
            'button_2_link' => ['required', 'string', 'max:255'],
            'right_card_title' => ['required', 'string', 'max:160'],
            'right_card_text' => ['required', 'string', 'max:1000'],
            'school_name' => ['required', 'string', 'max:160'],
            'order' => ['required', 'integer', 'min:1', 'max:4'],
            'is_active' => ['nullable', 'boolean'],
            'image' => [$imageRule, 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
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

        if (!$user->school_id) {
            abort(403, 'Hero slides require a school context.');
        }
    }

    private function scopedSlidesQuery(): Builder
    {
        return HeroSlide::query()->where('school_id', auth()->user()->school_id);
    }

    private function guardActiveSlideLimit(bool $shouldBeActive, ?int $ignoreId = null): void
    {
        if (!$shouldBeActive) {
            return;
        }

        $activeCount = $this->scopedSlidesQuery()
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('is_active', true)
            ->count();

        if ($activeCount >= 4) {
            throw ValidationException::withMessages([
                'is_active' => 'Maximum of 4 active hero slides allowed.',
            ]);
        }
    }

    private function syncSlideOrder(HeroSlide $slide, int $targetOrder): void
    {
        $otherSlides = $this->scopedSlidesQuery()
            ->where('id', '!=', $slide->id)
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->values();

        $targetOrder = max(1, min(4, $targetOrder));
        $targetOrder = min($targetOrder, $otherSlides->count() + 1);

        $otherSlides->splice($targetOrder - 1, 0, [$slide]);
        $this->persistOrderedSlides($otherSlides);
    }

    private function normalizeSlideOrder(): void
    {
        $slides = $this->scopedSlidesQuery()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $this->persistOrderedSlides($slides);
    }

    private function persistOrderedSlides(Collection $slides): void
    {
        $slides->values()->each(function (HeroSlide $slide, int $index): void {
            $slide->timestamps = false;
            $slide->order = $index + 1;
            $slide->saveQuietly();
        });
    }

    private function deleteImageIfPresent(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
